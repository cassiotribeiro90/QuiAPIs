<?php
// controllers/api/app/ProdutoDetailController.php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\models\api\app\Produto;
use app\models\api\app\ProdutoOpcaoAdicional;
use app\models\api\app\AtributoOpcao;
use app\models\api\app\AtributoCategoria;
use app\controllers\api\app\AppControllerBase;

class ProdutoDetailController extends AppControllerBase
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'detalhe',
            ];
        }
        
        return $behaviors;
    }
    
    /**
     * GET /api/app/produto-detail?id={id}
     * Retorna detalhes completos do produto com suas opções
     * Suporta: simples, personalizavel, meio_a_meio, combo
     */
    public function actionDetalhe()
    {
        try {
            $request = Yii::$app->request;
            $id = (int)$request->get('id');
            
            if (!$id) {
                return ApiResponse::error('ID do produto não informado', 400);
            }
            
            $produto = Produto::find()
                ->where(['id' => $id, 'deletado_em' => null, 'ativo' => 1, 'disponivel' => 1])
                ->one();
            
            if (!$produto) {
                return ApiResponse::error('Produto não encontrado', 404);
            }
            
            $tipo = $produto->tipo ?? 'simples';
            
            // Resposta base
            $response = [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'descricao' => $produto->descricao,
                'imagem' => $produto->imagem,
                'tipo' => $tipo,
                'preco' => (float)$produto->preco,
                'preco_promocional' => $produto->preco_promocional ? (float)$produto->preco_promocional : null,
                'quantidade_maxima' => 99,
            ];
            
            // Buscar opções baseado no tipo
            switch ($tipo) {
                case 'personalizavel':
                    $opcoes = $this->getOpcoesDoProduto($produto->id);
                    if (!empty($opcoes)) {
                        $response['opcoes'] = $opcoes;
                    }
                    break;
                    
                case 'meio_a_meio':
                    $meioData = $this->getMeioAMeioData($produto);
                    $response = array_merge($response, $meioData);
                    break;
                    
                case 'combo':
                    $comboData = $this->getComboData($produto);
                    $response = array_merge($response, $comboData);
                    break;
                    
                case 'simples':
                default:
                    // Não adiciona opções extras
                    break;
            }
            
            return ApiResponse::success($response);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar produto: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar produto', 500);
        }
    }
    
    /**
     * Busca opções de atributos para produtos personalizáveis
     */
    private function getOpcoesDoProduto($produtoId)
    {
        $sql = "
            SELECT 
                ac.id as categoria_id,
                ac.nome as categoria_nome,
                ac.tipo_selecao,
                ac.obrigatorio,
                ac.minimo,
                ac.maximo,
                ac.icone as categoria_icone,
                ao.id as opcao_id,
                ao.nome as opcao_nome,
                ao.descricao as opcao_descricao,
                ao.icone as opcao_icone,
                COALESCE(poa.preco_adicional, ao.preco_adicional) as preco_final
            FROM produto_opcao_adicional poa
            JOIN atributo_opcao ao ON poa.opcao_id = ao.id
            JOIN atributo_categoria ac ON ao.categoria_id = ac.id
            WHERE poa.produto_id = :produto_id
                AND poa.disponivel = 1
                AND ac.ativo = 1
            ORDER BY ac.ordem ASC, ao.ordem ASC
        ";
        
        $opcoesRaw = Yii::$app->db->createCommand($sql, [':produto_id' => $produtoId])->queryAll();
        
        if (empty($opcoesRaw)) {
            return [];
        }
        
        // Agrupar por categoria
        $categorias = [];
        foreach ($opcoesRaw as $opcao) {
            $categoriaId = $opcao['categoria_id'];
            
            if (!isset($categorias[$categoriaId])) {
                $categorias[$categoriaId] = [
                    'id' => (int)$categoriaId,
                    'nome' => $opcao['categoria_nome'],
                    'tipo_selecao' => $opcao['tipo_selecao'],
                    'obrigatorio' => (bool)$opcao['obrigatorio'],
                    'minimo' => (int)$opcao['minimo'],
                    'maximo' => (int)$opcao['maximo'],
                    'icone' => $opcao['categoria_icone'],
                    'opcoes' => [],
                ];
            }
            
            $categorias[$categoriaId]['opcoes'][] = [
                'id' => (int)$opcao['opcao_id'],
                'nome' => $opcao['opcao_nome'],
                'descricao' => $opcao['opcao_descricao'],
                'preco_adicional' => (float)$opcao['preco_final'],
                'icone' => $opcao['opcao_icone'],
            ];
        }
        
        return array_values($categorias);
    }
    
    /**
     * Busca dados para produto meio a meio
     */
    private function getMeioAMeioData($produto)
    {
        // Buscar configuração da loja
        $loja = $produto->loja;
        $config = json_decode($loja->configuracoes, true);
        $meioConfig = $config['meio_a_meio'] ?? [];
        
        // Buscar tamanhos (categoria 1)
        $tamanhos = $this->getOpcoesPorCategoria($produto->id, 1);
        
        // Buscar bordas (categoria 2)
        $bordas = $this->getOpcoesPorCategoria($produto->id, 2);
        
        // Buscar adicionais (categoria 3)
        $adicionais = $this->getOpcoesPorCategoria($produto->id, 3);
        
        // Buscar sabores salgados
        $saboresSalgados = [];
        if (isset($meioConfig['sabores_salgados']) && is_array($meioConfig['sabores_salgados'])) {
            $sabores = Produto::find()
                ->where(['id' => $meioConfig['sabores_salgados'], 'ativo' => 1, 'disponivel' => 1])
                ->orderBy(['nome' => SORT_ASC])
                ->all();
            
            foreach ($sabores as $s) {
                $saboresSalgados[] = [
                    'id' => $s->id,
                    'nome' => $s->nome,
                    'preco' => (float)$s->preco,
                    'preco_promocional' => $s->preco_promocional ? (float)$s->preco_promocional : null,
                    'imagem' => $s->imagem,
                    'descricao' => $s->descricao,
                ];
            }
        }
        
        // Buscar sabores doces
        $saboresDoces = [];
        if (isset($meioConfig['sabores_doces']) && is_array($meioConfig['sabores_doces'])) {
            $sabores = Produto::find()
                ->where(['id' => $meioConfig['sabores_doces'], 'ativo' => 1, 'disponivel' => 1])
                ->orderBy(['nome' => SORT_ASC])
                ->all();
            
            foreach ($sabores as $s) {
                $saboresDoces[] = [
                    'id' => $s->id,
                    'nome' => $s->nome,
                    'preco' => (float)$s->preco,
                    'preco_promocional' => $s->preco_promocional ? (float)$s->preco_promocional : null,
                    'imagem' => $s->imagem,
                    'descricao' => $s->descricao,
                ];
            }
        }
        
        // Buscar molhos (categoria 4) - opcional
        $molhos = $this->getOpcoesPorCategoria($produto->id, 4);
        
        // Buscar massas (categoria 5) - opcional
        $massas = $this->getOpcoesPorCategoria($produto->id, 5);
        
        return [
            'preco_base' => 0,
            'regra_preco' => $meioConfig['regra'] ?? 'media',
            'descricao_regra' => $this->getDescricaoRegra($meioConfig['regra'] ?? 'media'),
            'tamanhos' => $tamanhos,
            'bordas' => $bordas,
            'adicionais' => $adicionais,
            'molhos' => $molhos,
            'massas' => $massas,
            'sabores_salgados' => $saboresSalgados,
            'sabores_doces' => $saboresDoces,
        ];
    }
    
    /**
     * Busca dados para produto combo
     */
    private function getComboData($produto)
    {
        // Verificar se a tabela produto_combo_item existe
        $tableExists = Yii::$app->db->schema->getTableSchema('produto_combo_item');
        
        if (!$tableExists) {
            return [
                'itens' => [],
                'preco_normal' => (float)$produto->preco,
            ];
        }
        
        // Buscar itens do combo
        $sql = "SELECT pci.*, p.nome, p.preco, p.imagem
                FROM produto_combo_item pci
                JOIN produto p ON pci.produto_id = p.id
                WHERE pci.combo_id = :combo_id";
        
        $itens = Yii::$app->db->createCommand($sql, [':combo_id' => $produto->id])->queryAll();
        
        $itensFormatados = array_map(function($item) {
            return [
                'produto_id' => (int)$item['produto_id'],
                'nome' => $item['nome'],
                'quantidade' => (int)$item['quantidade'],
                'preco_unitario' => (float)$item['preco'],
                'imagem' => $item['imagem'],
            ];
        }, $itens);
        
        // Calcular preço normal (soma dos itens)
        $precoNormal = array_sum(array_map(function($item) {
            return $item['preco_unitario'] * $item['quantidade'];
        }, $itensFormatados));
        
        return [
            'preco_normal' => $precoNormal,
            'itens' => $itensFormatados,
        ];
    }
    
    /**
     * Busca opções de uma categoria específica para um produto
     */
    private function getOpcoesPorCategoria($produtoId, $categoriaId)
    {
        $sql = "
            SELECT 
                ao.id,
                ao.nome,
                ao.descricao,
                COALESCE(poa.preco_adicional, ao.preco_adicional) as preco_adicional,
                ao.icone
            FROM produto_opcao_adicional poa
            JOIN atributo_opcao ao ON poa.opcao_id = ao.id
            WHERE poa.produto_id = :produto_id
                AND ao.categoria_id = :categoria_id
                AND poa.disponivel = 1
            ORDER BY ao.ordem
        ";
        
        $opcoes = Yii::$app->db->createCommand($sql, [
            ':produto_id' => $produtoId,
            ':categoria_id' => $categoriaId,
        ])->queryAll();
        
        return array_map(function($opcao) {
            return [
                'id' => (int)$opcao['id'],
                'nome' => $opcao['nome'],
                'descricao' => $opcao['descricao'],
                'preco_adicional' => (float)$opcao['preco_adicional'],
                'icone' => $opcao['icone'],
            ];
        }, $opcoes);
    }
    
    /**
     * Retorna descrição da regra de preço
     */
    private function getDescricaoRegra($regra)
    {
        switch ($regra) {
            case 'maior':
                return 'Preço baseado no sabor de maior valor';
            case 'fixo':
                return 'Preço fixo para qualquer combinação';
            case 'media':
            default:
                return 'Preço baseado na média dos dois sabores';
        }
    }
}