<?php
// controllers/api/app/CarrinhoController.php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\models\api\app\Produto;
use app\models\api\app\Carrinho;
use app\models\api\app\CarrinhoItem;
use app\models\api\app\ProdutoOpcaoAdicional;
use app\models\api\app\AtributoOpcao;
use app\models\api\app\AppEndereco;
use app\controllers\api\app\AppControllerBase;

class CarrinhoController extends AppControllerBase
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'calcular',
            ];
        }
        
        return $behaviors;
    }
    
    /**
     * GET /api/app/carrinho
     * Retorna todos os itens do carrinho do usuário atual
     * @param int|null $enderecoId Parâmetro opcional para calcular taxa de entrega
     */
    public function actionIndex($enderecoId = null)
    {
        try {
            $usuarioId = Yii::$app->user->id;
            
            if (!$usuarioId) {
                return ApiResponse::error('Usuário não autenticado', 401);
            }
            
            if ($enderecoId === null) {
                $enderecoId = Yii::$app->request->get('endereco_id');
            }
            
            $carrinho = Carrinho::find()
                ->where(['usuario_id' => $usuarioId, 'status' => 'ativo'])
                ->one();
            
            if (!$carrinho) {
                return ApiResponse::success([
                    'itens' => [],
                    'resumo' => $this->buildResumoVazio(),
                ]);
            }
            
            $itens = CarrinhoItem::find()
                ->where(['carrinho_id' => $carrinho->id])
                ->all();
            
            $loja = $carrinho->loja;
            $itensFormatados = $this->formatarItens($itens);
            
            $resumo = $this->buildResumoBase($carrinho, $loja);
            $this->enriquecerResumoComFrete($resumo, $carrinho, $loja, $enderecoId, $usuarioId);
            $this->enriquecerResumoComFormasPagamento($resumo, $loja);
            
            return ApiResponse::success([
                'itens' => $itensFormatados,
                'resumo' => $resumo,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar carrinho: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar carrinho', 500);
        }
    }

    /**
     * Enriquece o resumo com as formas de pagamento disponíveis da loja
     */
    private function enriquecerResumoComFormasPagamento(&$resumo, $loja)
    {
        if (!$loja) {
            $resumo['formas_pagamento'] = [];
            return;
        }
        
        $config = $loja->configuracoes;
        
        if (is_string($config)) {
            $config = json_decode($config, true);
        }
        
        if (!is_array($config) || !isset($config['formas_pagamento'])) {
            // Valores padrão caso a loja não tenha configuração
            $resumo['formas_pagamento'] = [
                'dinheiro' => ['label' => 'Dinheiro', 'troco' => true],
                'cartao_entrega' => ['label' => 'Cartão na entrega', 'maquininha' => true],
            ];
            return;
        }
        
        $resumo['formas_pagamento'] = $config['formas_pagamento'];
    }

    /**
     * Constroi resumo vazio (carrinho sem itens)
     */
    private function buildResumoVazio()
    {
        return [
            'total_itens' => 0,
            'subtotal' => 0.0,
            'loja_id' => null,
            'loja_nome' => null,
            'taxa_entrega' => null,
            'total' => 0.0,
            'distancia_km' => null,
            'formas_pagamento' => [],
        ];
    }

    /**
     * Constroi resumo base (sem frete)
     */
    private function buildResumoBase($carrinho, $loja)
    {
        return [
            'total_itens' => (int)$carrinho->total_itens,
            'subtotal' => (float)$carrinho->subtotal,
            'loja_id' => $carrinho->loja_id,
            'loja_nome' => $loja ? $loja->nome : null,
            'taxa_entrega' => null,
            'total' => (float)$carrinho->subtotal,
            'distancia_km' => null,
            'formas_pagamento' => [],
        ];
    }
    
    /**
     * PUT /api/app/carrinho/atualizar
     * Atualiza a quantidade e/ou observação de um item do carrinho
     * Se quantidade = 0, remove o item automaticamente
     */
    public function actionAtualizar()
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $request = Yii::$app->request;
            $usuarioId = Yii::$app->user->id;
            
            if (!$usuarioId) {
                return ApiResponse::error('Usuário não autenticado', 401);
            }
            
            $itemId = $request->post('item_id');
            $produtoId = $request->post('produto_id');
            $quantidade = $request->post('quantidade') !== null ? (int)$request->post('quantidade') : null;
            $opcoes = $request->post('opcoes', []);
            $observacao = $request->post('observacao');
            $enderecoId = $request->post('endereco_id'); // opcional, para retornar frete no resumo
            
            if (!$itemId && !$produtoId) {
                return ApiResponse::error('ID do item ou ID do produto não informado', 400);
            }
            
            if ($quantidade !== null && $quantidade < 0) {
                return ApiResponse::error('Quantidade inválida', 400);
            }
            
            $carrinho = null;
            
            // CASO 1: Atualizar/Remover item existente (tem item_id)
            if ($itemId) {
                $item = CarrinhoItem::findOne($itemId);
                
                if (!$item) {
                    return ApiResponse::error('Item não encontrado', 404);
                }
                
                $carrinho = Carrinho::findOne($item->carrinho_id);
                if (!$carrinho || $carrinho->usuario_id != $usuarioId) {
                    return ApiResponse::error('Acesso negado', 403);
                }
                
                if ($quantidade === 0) {
                    $item->delete();
                } else {
                    if ($quantidade !== null) {
                        $precoUnitario = (float)$item->preco_unitario;
                        $precoAdicionais = (float)$item->preco_adicionais;
                        $item->quantidade = $quantidade;
                        $item->preco_total = ($precoUnitario + $precoAdicionais) * $quantidade;
                    }
                    
                    if ($observacao !== null) {
                        $item->observacao = $observacao;
                    }
                    
                    $item->save();
                }
            }
            // CASO 2: Adicionar novo item (tem produto_id)
            else {
                $produto = Produto::find()
                    ->where(['id' => $produtoId, 'deletado_em' => null, 'ativo' => 1, 'disponivel' => 1])
                    ->one();
                
                if (!$produto) {
                    return ApiResponse::error('Produto não encontrado ou indisponível', 404);
                }
                
                $carrinho = Carrinho::find()
                    ->where(['usuario_id' => $usuarioId, 'status' => 'ativo'])
                    ->one();
                
                if ($carrinho && $carrinho->loja_id != $produto->loja_id) {
                    $transaction->rollBack();
                    return ApiResponse::error(
                        'Seu carrinho já tem itens de outra loja. Limpe o carrinho primeiro.',
                        409,
                        ['acao' => 'limpar_carrinho', 'loja_atual' => $carrinho->loja_id, 'nova_loja' => $produto->loja_id]
                    );
                }
                
                if (!$carrinho) {
                    $carrinho = new Carrinho();
                    $carrinho->usuario_id = $usuarioId;
                    $carrinho->loja_id = $produto->loja_id;
                    $carrinho->status = 'ativo';
                    $carrinho->total_itens = 0;
                    $carrinho->subtotal = 0;
                    $carrinho->save();
                }
                
                $precoAdicionais = 0;
                $opcoesDetalhes = [];
                
                if (!empty($opcoes)) {
                    foreach ($opcoes as $opcaoId) {
                        $opcao = ProdutoOpcaoAdicional::find()
                            ->where(['produto_id' => $produtoId, 'opcao_id' => $opcaoId])
                            ->one();
                        
                        if ($opcao && $opcao->preco_adicional !== null) {
                            $precoAdicionais += (float)$opcao->preco_adicional;
                            $opcoesDetalhes[] = [
                                'id' => $opcaoId,
                                'nome' => $opcao->opcao->nome ?? '',
                                'preco_adicional' => (float)$opcao->preco_adicional,
                            ];
                        } else {
                            $opcaoPadrao = AtributoOpcao::findOne($opcaoId);
                            if ($opcaoPadrao) {
                                $precoAdicionais += (float)$opcaoPadrao->preco_adicional;
                                $opcoesDetalhes[] = [
                                    'id' => $opcaoId,
                                    'nome' => $opcaoPadrao->nome,
                                    'preco_adicional' => (float)$opcaoPadrao->preco_adicional,
                                ];
                            }
                        }
                    }
                }
                
                $precoUnitario = (float)$produto->preco;
                $qtd = $quantidade ?? 1;
                $precoTotalItem = ($precoUnitario + $precoAdicionais) * $qtd;
                
                $itemExistente = CarrinhoItem::find()
                    ->where([
                        'carrinho_id' => $carrinho->id,
                        'produto_id' => $produtoId,
                        'opcoes' => json_encode($opcoes),
                    ])
                    ->one();
                
                if ($itemExistente) {
                    $novaQuantidade = $qtd;
                    $itemExistente->quantidade = $novaQuantidade;
                    $itemExistente->preco_total = ($precoUnitario + $precoAdicionais) * $novaQuantidade;
                    if ($observacao !== null) {
                        $itemExistente->observacao = $observacao;
                    }
                    $itemExistente->save();
                } else {
                    $item = new CarrinhoItem();
                    $item->carrinho_id = $carrinho->id;
                    $item->produto_id = $produtoId;
                    $item->quantidade = $qtd;
                    $item->preco_unitario = $precoUnitario;
                    $item->preco_adicionais = $precoAdicionais;
                    $item->preco_total = $precoTotalItem;
                    $item->produto_nome = $produto->nome;
                    $item->produto_descricao = $produto->descricao;
                    $item->produto_imagem = $produto->imagem;
                    $item->opcoes = json_encode($opcoes);
                    $item->opcoes_detalhes = json_encode($opcoesDetalhes);
                    $item->observacao = $observacao;
                    $item->save();
                }
            }
            
            $itensRestantes = CarrinhoItem::find()
                ->where(['carrinho_id' => $carrinho->id])
                ->count();
            
            if ($itensRestantes == 0) {
                $carrinho->delete();
                $transaction->commit();
                
                return ApiResponse::success([
                    'itens' => [],
                    'resumo' => $this->buildResumoVazio(),
                ]);
            }
            
            $this->atualizarResumoCarrinho($carrinho->id);
            $carrinho->refresh();
            
            $itens = CarrinhoItem::find()
                ->with(['produto'])
                ->where(['carrinho_id' => $carrinho->id])
                ->all();
            
            $itensFormatados = $this->formatarItens($itens);
            $loja = $carrinho->loja;
            
            $resumo = $this->buildResumoBase($carrinho, $loja);
            $this->enriquecerResumoComFrete($resumo, $carrinho, $loja, $enderecoId, $usuarioId);
            
            $transaction->commit();
            
            return ApiResponse::success([
                'itens' => $itensFormatados,
                'resumo' => $resumo,
            ]);
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Erro ao atualizar carrinho: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao processar requisição: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/app/carrinho/limpar
     * Limpa todo o carrinho
     */
    public function actionLimpar()
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $usuarioId = Yii::$app->user->id;
            
            if (!$usuarioId) {
                return ApiResponse::error('Usuário não autenticado', 401);
            }
            
            $carrinho = Carrinho::find()
                ->where(['usuario_id' => $usuarioId, 'status' => 'ativo'])
                ->one();
            
            if ($carrinho) {
                $carrinho->delete();
            }
            
            $transaction->commit();
            
            return ApiResponse::success([
                'mensagem' => 'Carrinho limpo com sucesso',
                'itens' => [],
                'resumo' => $this->buildResumoVazio(),
            ]);
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Erro ao limpar carrinho: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao limpar carrinho', 500);
        }
    }
    
    /**
     * POST /api/app/carrinho/calcular
     * Calcula o preço de um item sem adicionar ao carrinho
     */
    public function actionCalcular()
    {
        try {
            $request = Yii::$app->request;
            $produtoId = (int)$request->post('produto_id');
            $opcoesIds = $request->post('opcoes', []);
            $quantidade = (int)$request->post('quantidade', 1);
            
            if (!$produtoId) {
                return ApiResponse::error('ID do produto não informado', 400);
            }
            
            $produto = Produto::findOne($produtoId);
            
            if (!$produto) {
                return ApiResponse::error('Produto não encontrado', 404);
            }
            
            $precoBase = (float)$produto->preco;
            $precoAdicionais = 0;
            $opcoesDetalhes = [];
            
            foreach ($opcoesIds as $opcaoId) {
                $opcao = ProdutoOpcaoAdicional::find()
                    ->where(['produto_id' => $produtoId, 'opcao_id' => $opcaoId])
                    ->one();
                
                if ($opcao && $opcao->preco_adicional !== null) {
                    $precoAdicionais += (float)$opcao->preco_adicional;
                    $opcoesDetalhes[] = [
                        'id' => $opcaoId,
                        'nome' => $opcao->opcao->nome ?? '',
                        'preco_adicional' => (float)$opcao->preco_adicional,
                    ];
                } else {
                    $opcaoPadrao = AtributoOpcao::findOne($opcaoId);
                    if ($opcaoPadrao) {
                        $precoAdicionais += (float)$opcaoPadrao->preco_adicional;
                        $opcoesDetalhes[] = [
                            'id' => $opcaoId,
                            'nome' => $opcaoPadrao->nome,
                            'preco_adicional' => (float)$opcaoPadrao->preco_adicional,
                        ];
                    }
                }
            }
            
            $precoUnitario = $precoBase + $precoAdicionais;
            $precoTotal = $precoUnitario * $quantidade;
            
            return ApiResponse::success([
                'produto_id' => $produtoId,
                'produto_nome' => $produto->nome,
                'preco_base' => $precoBase,
                'preco_adicionais' => $precoAdicionais,
                'preco_unitario' => $precoUnitario,
                'preco_total' => $precoTotal,
                'quantidade' => $quantidade,
                'opcoes' => $opcoesDetalhes,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao calcular preço: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao calcular preço', 500);
        }
    }
    
    /**
     * GET /api/app/carrinho/resumo
     * Retorna apenas o resumo do carrinho
     * @param int|null $enderecoId Parâmetro opcional para calcular taxa de entrega
     */
    public function actionResumo($enderecoId = null)
    {
        try {
            $usuarioId = Yii::$app->user->id;
            
            if (!$usuarioId) {
                return ApiResponse::error('Usuário não autenticado', 401);
            }
            
            if ($enderecoId === null) {
                $enderecoId = Yii::$app->request->get('endereco_id');
            }
            
            $carrinho = Carrinho::find()
                ->where(['usuario_id' => $usuarioId, 'status' => 'ativo'])
                ->one();
            
            if (!$carrinho) {
                return ApiResponse::success($this->buildResumoVazio());
            }
            
            $loja = $carrinho->loja;
            $resumo = $this->buildResumoBase($carrinho, $loja);
            $this->enriquecerResumoComFrete($resumo, $carrinho, $loja, $enderecoId, $usuarioId);
            
            return ApiResponse::success($resumo);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar resumo: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar resumo', 500);
        }
    }
    
    /**
     * GET /api/app/carrinho/verificar-loja
     * Verifica se o carrinho tem itens de outra loja
     */
    public function actionVerificarLoja()
    {
        try {
            $request = Yii::$app->request;
            $usuarioId = Yii::$app->user->id;
            $lojaId = (int)$request->get('loja_id');
            
            if (!$usuarioId) {
                return ApiResponse::error('Usuário não autenticado', 401);
            }
            
            $carrinho = Carrinho::find()
                ->where(['usuario_id' => $usuarioId, 'status' => 'ativo'])
                ->one();
            
            if (!$carrinho) {
                return ApiResponse::success([
                    'mesma_loja' => true,
                    'carrinho_vazio' => true,
                ]);
            }
            
            return ApiResponse::success([
                'mesma_loja' => $carrinho->loja_id == $lojaId,
                'carrinho_vazio' => false,
                'loja_id_atual' => $carrinho->loja_id,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao verificar loja: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao verificar loja', 500);
        }
    }

    /**
     * Atualiza os campos de resumo do carrinho (total_itens, subtotal)
     */
    private function atualizarResumoCarrinho($carrinhoId)
    {
        $sql = "
            SELECT 
                COALESCE(SUM(quantidade), 0) as total_itens,
                COALESCE(SUM(preco_total), 0) as subtotal
            FROM carrinho_item
            WHERE carrinho_id = :carrinho_id
        ";
        
        $result = Yii::$app->db->createCommand($sql, [':carrinho_id' => $carrinhoId])->queryOne();
        
        $carrinho = Carrinho::findOne($carrinhoId);
        if ($carrinho) {
            $carrinho->total_itens = (int)$result['total_itens'];
            $carrinho->subtotal = (float)$result['subtotal'];
            $carrinho->save(false);
        }
    }
    
    /**
     * Formata os itens do carrinho para retorno da API
     */
    private function formatarItens($itens)
    {
        $result = [];
        
        foreach ($itens as $item) {
            $opcoes = $item->opcoes;
            if (is_string($opcoes)) {
                $opcoes = json_decode($opcoes, true);
            }
            if (!is_array($opcoes)) {
                $opcoes = [];
            }
            
            $opcoesDetalhes = $item->opcoes_detalhes;
            if (is_string($opcoesDetalhes)) {
                $opcoesDetalhes = json_decode($opcoesDetalhes, true);
            }
            if (!is_array($opcoesDetalhes)) {
                $opcoesDetalhes = [];
            }
            
            $metadata = $item->metadata;
            if (is_string($metadata)) {
                $metadata = json_decode($metadata, true);
            }
            if (!is_array($metadata)) {
                $metadata = [];
            }
            
            $result[] = [
                'id' => (int)$item->id,
                'produto_id' => (int)$item->produto_id,
                'nome' => $item->produto_nome,
                'descricao' => $item->produto_descricao,
                'imagem' => $item->produto_imagem,
                'quantidade' => (int)$item->quantidade,
                'preco_unitario' => (float)$item->preco_unitario,
                'preco_adicionais' => (float)$item->preco_adicionais,
                'preco_total' => (float)$item->preco_total,
                'opcoes' => $opcoes,
                'opcoes_detalhes' => $opcoesDetalhes,
                'observacao' => $item->observacao,
                'metadata' => $metadata,
            ];
        }
        
        return $result;
    }

    /**
     * Calcula a distância entre dois pontos (Haversine)
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
    
    /**
     * Calcula a taxa de entrega baseada na distância
     * Regra: até 5km = R$10,00; 5-10km = R$15,00; acima de 10km = R$20,00
     */
    private function calcularTaxaEntrega($distancia)
    {
        if ($distancia <= 5) {
            return 10.00;
        } elseif ($distancia <= 10) {
            return 15.00;
        } else {
            return 20.00;
        }
    }
    
    /**
     * Enriquece o resumo com taxa de entrega, total e distância (se endereco_id válido)
     */
    private function enriquecerResumoComFrete(&$resumo, $carrinho, $loja, $enderecoId, $usuarioId)
    {
        if (!$enderecoId) {
            return;
        }
        
        $endereco = AppEndereco::find()
            ->where(['id' => $enderecoId, 'usuario_id' => $usuarioId])
            ->one();
            
        if (!$endereco || !$loja) {
            return;
        }
        
        if (!$loja->latitude || !$loja->longitude || !$endereco->latitude || !$endereco->longitude) {
            return;
        }
        
        $distancia = $this->calcularDistancia(
            $loja->latitude, $loja->longitude,
            $endereco->latitude, $endereco->longitude
        );
        
        $taxaEntrega = $this->calcularTaxaEntrega($distancia);
        
        $resumo['taxa_entrega'] = $taxaEntrega;
        $resumo['total'] = (float)$carrinho->subtotal + $taxaEntrega;
        $resumo['distancia_km'] = round($distancia, 2);
    }
}