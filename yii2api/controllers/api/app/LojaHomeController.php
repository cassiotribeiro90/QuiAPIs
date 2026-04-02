<?php
// controllers/api/app/LojaHomeController.php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\components\DistanceCalculator;
use app\models\api\app\Loja;
use app\models\api\app\Produto;
use app\models\api\app\Subcategoria;
use app\controllers\api\app\AppControllerBase;

class LojaHomeController extends AppControllerBase
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'index',
                'search',
                'categorias',
                'avaliacoes',
            ];
        }
        
        return $behaviors;
    }
    
    /**
     * GET /api/app/loja-home
     * GET /api/app/loja-home/{id}
     * GET /api/app/loja-home?id={id}
     */
    public function actionIndex($id = null)
    {
        try {
            $request = Yii::$app->request;
            
            if ($id === null) {
                $id = $request->get('id');
            }
            if ($id === null) {
                $id = $request->get('loja_id');
            }
            
            // Força como inteiro
            $id = (int)$id;
            
            if (!$id) {
                return ApiResponse::error('ID da loja não informado', 400);
            }
            
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            
            // ===== DEBUG: INFORMAÇÕES DA LOJA =====
            $debug = [];
            
            // Busca a loja
            $loja = Loja::find()
                ->where(['id' => $id, 'deletado_em' => null])
                ->one();
            
            if (!$loja) {
                return ApiResponse::error('Loja não encontrada', 404);
            }
            
            $debug['loja_encontrada'] = [
                'id' => $loja->id,
                'nome' => $loja->nome,
            ];
            
            // ===== DEBUG: CONTAGEM DE PRODUTOS =====
            $totalProdutos = Produto::find()
                ->where(['loja_id' => $id, 'deletado_em' => null])
                ->count();
            $debug['total_produtos_loja'] = $totalProdutos;
            
            $produtosAtivos = Produto::find()
                ->where([
                    'loja_id' => $id,
                    'deletado_em' => null,
                    'ativo' => 1
                ])
                ->count();
            $debug['produtos_ativos'] = $produtosAtivos;
            
            $produtosDisponiveis = Produto::find()
                ->where([
                    'loja_id' => $id,
                    'deletado_em' => null,
                    'disponivel' => 1
                ])
                ->count();
            $debug['produtos_disponiveis'] = $produtosDisponiveis;
            
            $produtosAtivosEDisponiveis = Produto::find()
                ->where([
                    'loja_id' => $id,
                    'deletado_em' => null,
                    'ativo' => 1,
                    'disponivel' => 1
                ])
                ->count();
            $debug['produtos_ativos_e_disponiveis'] = $produtosAtivosEDisponiveis;
            
            // ===== DEBUG: PRODUTOS COM SUBCATEGORIA =====
            $produtosComSubcategoria = Produto::find()
                ->where([
                    'loja_id' => $id,
                    'deletado_em' => null,
                    'ativo' => 1,
                    'disponivel' => 1
                ])
                ->andWhere(['not', ['subcategoria_id' => null]])
                ->count();
            $debug['produtos_com_subcategoria'] = $produtosComSubcategoria;
            
            // ===== DEBUG: LISTA DE PRODUTOS (primeiros 5) =====
            $primeirosProdutos = Produto::find()
                ->select(['id', 'nome', 'subcategoria_id', 'ativo', 'disponivel'])
                ->where(['loja_id' => $id, 'deletado_em' => null])
                ->limit(5)
                ->asArray()
                ->all();
            $debug['primeiros_produtos'] = $primeirosProdutos;
            
            // ===== DEBUG: SUBCATEGORIAS EXISTENTES =====
            $todasSubcategorias = Subcategoria::find()
                ->select(['id', 'nome', 'icone', 'ordem'])
                ->asArray()
                ->all();
            $debug['todas_subcategorias'] = $todasSubcategorias;
            
            // ===== DEBUG: QUERY DAS SEÇÕES =====
            $querySubcategorias = Subcategoria::find()
                ->select([
                    'subcategoria.id',
                    'subcategoria.nome',
                    'subcategoria.icone',
                    'subcategoria.ordem',
                ])
                ->innerJoin('produto', 'produto.subcategoria_id = subcategoria.id')
                ->where([
                    'produto.loja_id' => $id,
                    'produto.deletado_em' => null,
                    'produto.ativo' => 1,
                    'produto.disponivel' => 1,
                ])
                ->groupBy('subcategoria.id')
                ->orderBy(['subcategoria.ordem' => SORT_ASC]);
            
            $debug['sql_secoes'] = $querySubcategorias->createCommand()->getRawSql();
            
            // ===== CALCULA DISTÂNCIA =====
            $distancia = null;
            if ($latitude && $longitude && $loja->latitude && $loja->longitude) {
                $distancia = DistanceCalculator::euclidean(
                    (float)$latitude, (float)$longitude,
                    (float)$loja->latitude, (float)$loja->longitude
                );
            }
            
            // ===== SEÇÕES AGRUPADAS POR SUBCATEGORIA =====
            $secoes = $this->getSecoesPorSubcategoria($id);
            $debug['secoes_encontradas'] = count($secoes);
            
            $response = [
                'id' => $loja->id,
                'nome' => $loja->nome,
                'descricao' => $loja->descricao,
                'slug' => $loja->slug,
                'categoria' => $loja->categoria,
                'logo' => $loja->logo,
                'capa' => $loja->capa,
                'cidade' => $loja->cidade,
                'uf' => $loja->uf,
                'logradouro' => $loja->logradouro,
                'numero' => $loja->numero,
                'complemento' => $loja->complemento,
                'bairro' => $loja->bairro,
                'cep' => $loja->cep,
                'endereco_completo' => $loja->getEnderecoCompleto(),
                'endereco_resumido' => $loja->getEnderecoResumido(),
                'latitude' => $loja->latitude ? (float)$loja->latitude : null,
                'longitude' => $loja->longitude ? (float)$loja->longitude : null,
                'telefone' => $loja->telefone,
                'whatsapp' => $loja->whatsapp,
                'email' => $loja->email,
                'instagram' => $loja->instagram,
                'nota_media' => (float)$loja->nota_media,
                'total_avaliacoes' => (int)$loja->total_avaliacoes,
                'tempo_entrega_min' => (int)$loja->tempo_entrega_min,
                'tempo_entrega_max' => (int)$loja->tempo_entrega_max,
                'taxa_entrega' => (float)$loja->taxa_entrega,
                'pedido_minimo' => (float)$loja->pedido_minimo,
                'destaque' => (bool)$loja->destaque,
                'verificado' => (bool)$loja->verificado,
                'trending_score' => (int)$loja->trending_score,
                'fluxo_status' => $loja->fluxo_status,
                'cor_tema' => $loja->cor_tema,
                'configuracoes' => $loja->configuracoes,
                'secoes' => $secoes,
                '_debug' => $debug, // REMOVER DEPOIS DO DEBUG
            ];
            
            if ($distancia !== null) {
                $response['distancia'] = round($distancia, 2);
                $response['distancia_texto'] = $this->formatarDistancia($distancia);
            }
            
            return ApiResponse::success($response);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar loja: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar dados da loja: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Obtém seções agrupadas por subcategoria
     */
    private function getSecoesPorSubcategoria($lojaId)
    {
        try {
            $lojaId = (int)$lojaId;
            
            // SQL direto para buscar subcategorias com produtos
            $sql = "SELECT subcategoria.id, subcategoria.nome, subcategoria.icone, subcategoria.ordem 
                    FROM subcategoria 
                    INNER JOIN produto ON produto.subcategoria_id = subcategoria.id 
                    WHERE produto.loja_id = :loja_id 
                        AND produto.deletado_em IS NULL 
                        AND produto.ativo = 1 
                        AND produto.disponivel = 1 
                    GROUP BY subcategoria.id 
                    ORDER BY subcategoria.ordem";
            
            $subcategorias = Yii::$app->db->createCommand($sql, [':loja_id' => $lojaId])->queryAll();
            
            if (empty($subcategorias)) {
                return [];
            }
            
            $secoes = [];
            $ordem = 0;
            
            foreach ($subcategorias as $subcategoria) {
                // Buscar produtos para esta subcategoria
                $produtosSql = "SELECT * FROM produto 
                            WHERE loja_id = :loja_id 
                                AND subcategoria_id = :subcategoria_id 
                                AND deletado_em IS NULL 
                                AND ativo = 1 
                                AND disponivel = 1 
                            ORDER BY destaque DESC, nome ASC";
                
                $produtos = Yii::$app->db->createCommand($produtosSql, [
                    ':loja_id' => $lojaId,
                    ':subcategoria_id' => $subcategoria['id']
                ])->queryAll();
                
                if (empty($produtos)) {
                    continue;
                }
                
                $secoes[] = [
                    'id' => (int)$subcategoria['id'],
                    'nome' => $subcategoria['nome'],
                    'icone' => $subcategoria['icone'] ?: $this->getIconePorNome($subcategoria['nome']),
                    'ordem' => $ordem++,
                    'produtos' => array_map(function($produto) {
                        return [
                            'id' => (int)$produto['id'],
                            'nome' => $produto['nome'],
                            'descricao' => $produto['descricao'],
                            'preco' => (float)$produto['preco'],
                            'preco_promocional' => $produto['preco_promocional'] ? (float)$produto['preco_promocional'] : null,
                            'imagem' => $produto['imagem'],
                            'tempo_preparo' => $produto['tempo_preparo_min'] ? (int)$produto['tempo_preparo_min'] : null,
                            'disponivel' => (bool)$produto['disponivel'],
                            'destaque' => (bool)$produto['destaque'],
                            'ingredientes' => $produto['ingredientes'] ? json_decode($produto['ingredientes'], true) : null,
                            'opcionais' => $produto['opcoes'] ? json_decode($produto['opcoes'], true) : null,
                        ];
                    }, $produtos),
                ];
            }
            
            return $secoes;
            
        } catch (\Exception $e) {
            Yii::error("Erro em getSecoesPorSubcategoria: " . $e->getMessage(), __METHOD__);
            return [];
        }
    }
    
    /**
     * GET /api/app/loja-home/search
     */
    public function actionSearch()
    {
        try {
            $request = Yii::$app->request;
            
            $id = $request->get('id');
            $lojaId = $request->get('loja_id');
            $query = $request->get('q', '');
            $subcategoriaId = $request->get('subcategoria_id');
            
            $lojaId = $id ?: $lojaId;
            $lojaId = (int)$lojaId;
            
            if (!$lojaId) {
                return ApiResponse::error('ID da loja não informado', 400);
            }
            
            $lojaExists = Loja::find()
                ->where(['id' => $lojaId, 'deletado_em' => null])
                ->exists();
            
            if (!$lojaExists) {
                return ApiResponse::error('Loja não encontrada', 404);
            }
            
            $produtosQuery = Produto::find()
                ->where([
                    'loja_id' => $lojaId,
                    'deletado_em' => null,
                    'ativo' => 1,
                    'disponivel' => 1
                ])
                ->joinWith('subcategoria');
            
            if (!empty($query)) {
                $produtosQuery->andWhere([
                    'or',
                    ['like', 'produto.nome', $query],
                    ['like', 'produto.descricao', $query],
                ]);
            }
            
            if (!empty($subcategoriaId)) {
                $produtosQuery->andWhere(['produto.subcategoria_id' => (int)$subcategoriaId]);
            }
            
            $produtos = $produtosQuery
                ->orderBy([
                    'produto.destaque' => SORT_DESC,
                    'produto.nome' => SORT_ASC
                ])
                ->all();
            
            $grupos = [];
            foreach ($produtos as $produto) {
                $key = $produto->subcategoria_id ?? 0;
                $nomeSecao = $produto->subcategoria->nome ?? 'Outros';
                $iconeSecao = $produto->subcategoria->icone ?? '📦';
                $ordemSecao = $produto->subcategoria->ordem ?? 999;
                
                if (!isset($grupos[$key])) {
                    $grupos[$key] = [
                        'id' => $key,
                        'nome' => $nomeSecao,
                        'icone' => $iconeSecao,
                        'ordem' => $ordemSecao,
                        'produtos' => []
                    ];
                }
                $grupos[$key]['produtos'][] = $produto;
            }
            
            $secoes = array_values($grupos);
            usort($secoes, function($a, $b) {
                if ($a['id'] == 0) return 1;
                if ($b['id'] == 0) return -1;
                return $a['ordem'] <=> $b['ordem'];
            });
            
            $secoesFormatadas = [];
            $ordem = 0;
            foreach ($secoes as $secao) {
                $secoesFormatadas[] = [
                    'id' => $secao['id'],
                    'nome' => $secao['nome'],
                    'icone' => $secao['icone'],
                    'ordem' => $ordem++,
                    'produtos' => array_map(function($produto) {
                        return [
                            'id' => $produto->id,
                            'nome' => $produto->nome,
                            'descricao' => $produto->descricao,
                            'preco' => (float)$produto->preco,
                            'preco_promocional' => $produto->preco_promocional ? (float)$produto->preco_promocional : null,
                            'imagem' => $produto->imagem,
                            'tempo_preparo' => $produto->tempo_preparo ? (int)$produto->tempo_preparo : null,
                            'disponivel' => (bool)$produto->disponivel,
                            'destaque' => (bool)$produto->destaque,
                            'subcategoria_id' => $produto->subcategoria_id,
                            'subcategoria_nome' => $produto->subcategoria->nome ?? null,
                            'ingredientes' => $produto->ingredientes,
                            'opcionais' => $produto->opcionais,
                        ];
                    }, $secao['produtos']),
                ];
            }
            
            return ApiResponse::success([
                'secoes' => $secoesFormatadas,
                'total' => count($produtos),
                'search_query' => $query,
                'loja_id' => $lojaId,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao buscar produtos: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao buscar produtos', 500);
        }
    }
    
    /**
     * GET /api/app/loja-home/categorias
     */
    public function actionCategorias()
    {
        try {
            $request = Yii::$app->request;
            
            $id = $request->get('id');
            $lojaId = $request->get('loja_id');
            $lojaId = $id ?: $lojaId;
            $lojaId = (int)$lojaId;
            
            if (!$lojaId) {
                return ApiResponse::error('ID da loja não informado', 400);
            }
            
            $subcategorias = Subcategoria::find()
                ->select([
                    'subcategoria.id',
                    'subcategoria.nome',
                    'subcategoria.icone',
                    'subcategoria.ordem',
                    'COUNT(produto.id) as total_produtos'
                ])
                ->joinWith(['produtos' => function($query) use ($lojaId) {
                    $query->andWhere([
                        'produto.loja_id' => $lojaId,
                        'produto.deletado_em' => null,
                        'produto.ativo' => 1,
                        'produto.disponivel' => 1
                    ]);
                }])
                ->having(['>', 'COUNT(produto.id)', 0])
                ->groupBy('subcategoria.id')
                ->orderBy(['subcategoria.ordem' => SORT_ASC])
                ->all();
            
            $data = array_map(function($subcategoria) {
                return [
                    'id' => $subcategoria->id,
                    'nome' => $subcategoria->nome,
                    'icone' => $subcategoria->icone ?: $this->getIconePorNome($subcategoria->nome),
                    'ordem' => (int)$subcategoria->ordem,
                    'total_produtos' => (int)$subcategoria->total_produtos,
                ];
            }, $subcategorias);
            
            $produtosSemSubcategoria = Produto::find()
                ->where([
                    'loja_id' => $lojaId,
                    'subcategoria_id' => null,
                    'deletado_em' => null,
                    'ativo' => 1,
                    'disponivel' => 1
                ])
                ->count();
            
            if ($produtosSemSubcategoria > 0) {
                $data[] = [
                    'id' => 0,
                    'nome' => 'Outros',
                    'icone' => '📦',
                    'ordem' => 999,
                    'total_produtos' => (int)$produtosSemSubcategoria,
                ];
            }
            
            return ApiResponse::success([
                'categorias' => $data,
                'total' => count($data),
                'loja_id' => $lojaId,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar categorias: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar categorias', 500);
        }
    }
    
    /**
     * GET /api/app/loja-home/avaliacoes
     */
    public function actionAvaliacoes()
    {
        try {
            $request = Yii::$app->request;
            
            $id = $request->get('id');
            $lojaId = $request->get('loja_id');
            $lojaId = $id ?: $lojaId;
            $lojaId = (int)$lojaId;
            
            if (!$lojaId) {
                return ApiResponse::error('ID da loja não informado', 400);
            }
            
            return ApiResponse::success([
                'items' => [],
                'total' => 0,
                'media' => 0,
                'loja_id' => $lojaId,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar avaliações: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar avaliações', 500);
        }
    }
    
    private function getIconePorNome($nome)
    {
        $icones = [
            'Pizzas' => '🍕',
            'Massas' => '🍝',
            'Hambúrgueres' => '🍔',
            'Porções' => '🍟',
            'Saladas' => '🥗',
            'Entradas' => '🥨',
            'Sobremesas' => '🍰',
            'Doces' => '🍬',
            'Bebidas' => '🥤',
            'Sucos' => '🧃',
            'Refrigerantes' => '🥤',
            'Cervejas' => '🍺',
            'Cafés' => '☕',
            'Outros' => '📦',
        ];
        
        return $icones[$nome] ?? '🍽️';
    }
    
    private function formatarDistancia($distancia)
    {
        if ($distancia < 1) {
            return round($distancia * 1000) . 'm';
        }
        return number_format($distancia, 1, ',', '.') . 'km';
    }
}