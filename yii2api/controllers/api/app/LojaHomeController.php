<?php
// controllers/api/app/LojaHomeController.php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\components\DistanceCalculator;
use app\models\api\app\Loja;
use app\models\api\app\Produto;
use app\models\api\app\Subcategoria;
use app\models\api\app\AtributoOpcao;
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
                'categorias',
                'avaliacoes',
                'secoes',
            ];
        }
        
        return $behaviors;
    }
    
    /**
     * GET /api/app/loja-home
     * GET /api/app/loja-home/{id}
     * GET /api/app/loja-home?id={id}
     * 
     * Retorna:
     * - Dados da loja
     * - secoes (produtos agrupados por subcategoria)
     * - pagination (da seção atual)
     * - filter_options
     * - configuracoes (configurações da loja)
     * - meio_a_meio (opções de meio a meio)
     * 
     * Parâmetros:
     * - id / loja_id (obrigatório)
     * - categoria_id (opcional)
     * - search (opcional)
     * - order_by (opcional)
     * - page (opcional, padrão 1)
     * - per_page (opcional, padrão 20)
     */
    public function actionIndex($id = null)
    {
        try {
            $request = Yii::$app->request;
            
            // ===== ID DA LOJA =====
            if ($id === null) {
                $id = $request->get('id');
            }
            if ($id === null) {
                $id = $request->get('loja_id');
            }
            $lojaId = (int)$id;
            
            if (!$lojaId) {
                return ApiResponse::error('ID da loja não informado', 400);
            }
            
            // ===== PARÂMETROS DE FILTRO =====
            $categoriaId = $request->get('categoria_id') ? (int)$request->get('categoria_id') : null;
            $search = $request->get('search', '');
            $orderBy = $request->get('order_by', 'relevancia');
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            
            // ===== BUSCA A LOJA =====
            $loja = Loja::find()
                ->where(['id' => $lojaId, 'deletado_em' => null])
                ->one();
            
            if (!$loja) {
                return ApiResponse::error('Loja não encontrada', 404);
            }
            
            // ===== CALCULA DISTÂNCIA =====
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $distancia = null;
            if ($latitude && $longitude && $loja->latitude && $loja->longitude) {
                $distancia = DistanceCalculator::euclidean(
                    (float)$latitude, (float)$longitude,
                    (float)$loja->latitude, (float)$loja->longitude
                );
            }
            
            // ===== SEÇÕES COM PRODUTOS =====
            $secoesData = $this->getSecoesComProdutos($lojaId, $categoriaId, $search, $orderBy, $page, $perPage);
            
            // ===== FILTER OPTIONS =====
            $filterOptions = $this->getFilterOptions($lojaId);
            
            // ===== CONFIGURAÇÕES DA LOJA =====
            $configuracoes = [];
            if ($loja->configuracoes) {
                if (is_array($loja->configuracoes)) {
                    $configuracoes = $loja->configuracoes;
                } elseif (is_string($loja->configuracoes)) {
                    $configuracoes = json_decode($loja->configuracoes, true);
                    if (!is_array($configuracoes)) {
                        $configuracoes = [];
                    }
                }
            }
            
            // ===== OPÇÕES DE MEIO A MEIO =====
            $meioAMeio = null;
            if (isset($configuracoes['meio_a_meio']) && $configuracoes['meio_a_meio']) {
                $meioAMeio = $this->getMeioAMeioOptions($lojaId, $configuracoes['meio_a_meio']);
            }
            
            // ===== RESPOSTA =====
            $response = [
                // Dados da loja
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
                'status' => $loja->status,
                'fluxo_status' => $loja->fluxo_status,
                'cor_tema' => $loja->cor_tema,
                'configuracoes' => $configuracoes,
                'meio_a_meio' => $meioAMeio,
                // Produtos agrupados por seções
                'secoes' => $secoesData['secoes'],
                'pagination' => $secoesData['pagination'],
                // Filtros
                'filter_options' => $filterOptions,
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
     * GET /api/app/loja-home/categorias
     * Retorna as categorias disponíveis na loja
     */
    public function actionCategorias()
    {
        try {
            $request = Yii::$app->request;
            $lojaId = (int)$request->get('loja_id');
            
            if (!$lojaId) {
                return ApiResponse::error('ID da loja não informado', 400);
            }
            
            $filterOptions = $this->getFilterOptions($lojaId);
            
            return ApiResponse::success([
                'categorias' => $filterOptions['categorias'],
                'loja_id' => $lojaId,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar categorias: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar categorias', 500);
        }
    }
    
    /**
     * GET /api/app/loja-home/avaliacoes
     * Retorna as avaliações da loja
     */
    public function actionAvaliacoes()
    {
        try {
            $request = Yii::$app->request;
            $lojaId = (int)$request->get('loja_id');
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 10);
            
            if (!$lojaId) {
                return ApiResponse::error('ID da loja não informado', 400);
            }
            
            // TODO: Implementar busca de avaliações
            
            return ApiResponse::success([
                'items' => [],
                'pagination' => [
                    'total' => 0,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => 0,
                ],
                'loja_id' => $lojaId,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar avaliações: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar avaliações', 500);
        }
    }
    
    /**
     * GET /api/app/loja-home/secoes
     * Endpoint específico para buscar apenas as seções (sem produtos)
     */
    public function actionSecoes()
    {
        try {
            $request = Yii::$app->request;
            $lojaId = (int)$request->get('loja_id');
            
            if (!$lojaId) {
                return ApiResponse::error('ID da loja não informado', 400);
            }
            
            $sql = "SELECT DISTINCT s.id, s.nome, s.icone, s.ordem,
                           COUNT(p.id) as total_produtos
                    FROM subcategoria s
                    INNER JOIN produto p ON p.subcategoria_id = s.id
                    WHERE p.loja_id = :loja_id
                        AND p.deletado_em IS NULL
                        AND p.ativo = 1
                        AND p.disponivel = 1
                    GROUP BY s.id
                    ORDER BY s.ordem ASC";
            
            $secoes = Yii::$app->db->createCommand($sql, [':loja_id' => $lojaId])->queryAll();
            
            $data = array_map(function($secao) {
                return [
                    'id' => (int)$secao['id'],
                    'nome' => $secao['nome'],
                    'icone' => $secao['icone'] ?: $this->getIconePorNome($secao['nome']),
                    'ordem' => (int)$secao['ordem'],
                    'total_produtos' => (int)$secao['total_produtos'],
                ];
            }, $secoes);
            
            return ApiResponse::success([
                'items' => $data,
                'total' => count($data),
                'loja_id' => $lojaId,
            ]);
            
        } catch (\Exception $e) {
            Yii::error("Erro ao carregar seções: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao carregar seções', 500);
        }
    }
    
    /**
     * Busca produtos agrupados por seções (subcategorias) com paginação
     */
    private function getSecoesComProdutos($lojaId, $categoriaId, $search, $orderBy, $page, $perPage)
    {
        // Parâmetros da query
        $params = [':loja_id' => $lojaId];
        
        // Condições WHERE para produtos
        $whereConditions = [
            "p.loja_id = :loja_id",
            "p.deletado_em IS NULL",
            "p.ativo = 1",
            "p.disponivel = 1"
        ];
        
        if ($categoriaId && $categoriaId > 0) {
            $whereConditions[] = "p.subcategoria_id = :categoria_id";
            $params[':categoria_id'] = $categoriaId;
        }
        
        if (!empty($search) && trim($search) !== '') {
            $whereConditions[] = "(p.nome LIKE :search OR p.descricao LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        $whereClause = implode(" AND ", $whereConditions);
        
        // ===== BUSCAR SUBCATEGORIAS COM CONTAGEM TOTAL DE PRODUTOS =====
        $sqlSubcategorias = "SELECT s.id, s.nome, s.icone, s.ordem
                            FROM subcategoria s
                            INNER JOIN produto p ON p.subcategoria_id = s.id
                            WHERE {$whereClause}
                            GROUP BY s.id
                            ORDER BY s.ordem ASC";
        
        $subcategorias = Yii::$app->db->createCommand($sqlSubcategorias, $params)->queryAll();
        
        if (empty($subcategorias)) {
            return [
                'secoes' => [],
                'pagination' => [
                    'total' => 0,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => 0,
                ],
            ];
        }
        
        // ===== ORDENAÇÃO =====
        $orderSql = $this->getOrderBySql($orderBy);
        
        // ===== COLETA TODOS OS PRODUTOS DE TODAS AS SEÇÕES =====
        $todosProdutos = []; // lista plana de produtos com 'subcategoria_id'
        $totalProdutosGeral = 0;
        $secoesInfo = []; // guarda metadados das seções
        
        foreach ($subcategorias as $subcategoria) {
            $subId = (int)$subcategoria['id'];
            $prodParams = [':loja_id' => $lojaId, ':subcategoria_id' => $subId];
            
            $prodWhereConditions = [
                "p.loja_id = :loja_id",
                "p.subcategoria_id = :subcategoria_id",
                "p.deletado_em IS NULL",
                "p.ativo = 1",
                "p.disponivel = 1"
            ];
            
            if (!empty($search) && trim($search) !== '') {
                $prodWhereConditions[] = "(p.nome LIKE :search OR p.descricao LIKE :search)";
                $prodParams[':search'] = "%{$search}%";
            }
            
            $prodWhereClause = implode(" AND ", $prodWhereConditions);
            
            // Conta total de produtos da seção
            $countSql = "SELECT COUNT(*) FROM produto p WHERE {$prodWhereClause}";
            $totalSecao = (int)Yii::$app->db->createCommand($countSql, $prodParams)->queryScalar();
            $totalProdutosGeral += $totalSecao;
            
            // Busca todos os produtos da seção (sem LIMIT)
            $sqlProdutos = "SELECT p.* FROM produto p WHERE {$prodWhereClause} {$orderSql}";
            $produtosSecao = Yii::$app->db->createCommand($sqlProdutos, $prodParams)->queryAll();
            
            foreach ($produtosSecao as $produto) {
                $produto['subcategoria_id'] = $subId; // garante que está definido
                $todosProdutos[] = $produto;
            }
            
            $secoesInfo[$subId] = [
                'id' => $subId,
                'nome' => $subcategoria['nome'],
                'icone' => $subcategoria['icone'] ?: $this->getIconePorNome($subcategoria['nome']),
                'ordem' => (int)$subcategoria['ordem'],
                'total_produtos' => $totalSecao,
            ];
        }
        
        // ===== PRODUTOS SEM SUBCATEGORIA ("Outros") =====
        $outrosParams = [':loja_id' => $lojaId];
        $outrosWhereConditions = [
            "p.loja_id = :loja_id",
            "p.subcategoria_id IS NULL",
            "p.deletado_em IS NULL",
            "p.ativo = 1",
            "p.disponivel = 1"
        ];
        if (!empty($search) && trim($search) !== '') {
            $outrosWhereConditions[] = "(p.nome LIKE :search OR p.descricao LIKE :search)";
            $outrosParams[':search'] = "%{$search}%";
        }
        $outrosWhereClause = implode(" AND ", $outrosWhereConditions);
        
        $countOutrosSql = "SELECT COUNT(*) FROM produto p WHERE {$outrosWhereClause}";
        $totalOutros = (int)Yii::$app->db->createCommand($countOutrosSql, $outrosParams)->queryScalar();
        
        if ($totalOutros > 0) {
            $sqlOutrosProdutos = "SELECT p.* FROM produto p WHERE {$outrosWhereClause} {$orderSql}";
            $produtosOutros = Yii::$app->db->createCommand($sqlOutrosProdutos, $outrosParams)->queryAll();
            
            foreach ($produtosOutros as $produto) {
                $produto['subcategoria_id'] = 0; // marca como "Outros"
                $todosProdutos[] = $produto;
            }
            
            $secoesInfo[0] = [
                'id' => 0,
                'nome' => 'Outros',
                'icone' => '📦',
                'ordem' => 999,
                'total_produtos' => $totalOutros,
            ];
            
            $totalProdutosGeral += $totalOutros;
        }
        
        // ===== APLICAR PAGINAÇÃO GLOBAL =====
        $offset = ($page - 1) * $perPage;
        $produtosPaginados = array_slice($todosProdutos, $offset, $perPage);
        
        // ===== REAGRUPAR PRODUTOS PAGINADOS POR SEÇÃO =====
        $secoes = [];
        foreach ($secoesInfo as $subId => $info) {
            $produtosDaSecao = array_values(array_filter(
                $produtosPaginados,
                function ($produto) use ($subId) {
                    return (int)$produto['subcategoria_id'] === (int)$subId;
                }
            ));
            
            $secoes[] = [
                'id' => $info['id'],
                'nome' => $info['nome'],
                'icone' => $info['icone'],
                'ordem' => $info['ordem'],
                'total_produtos' => $info['total_produtos'],
                'produtos' => array_map(function ($produto) {
                    return [
                        'id' => (int)$produto['id'],
                        'nome' => $produto['nome'],
                        'tipo' => $produto['tipo'] ?? 'simples',
                        'descricao' => $produto['descricao'],
                        'preco' => (float)$produto['preco'],
                        'preco_promocional' => $produto['preco_promocional'] ? (float)$produto['preco_promocional'] : null,
                        'imagem' => $produto['imagem'],
                        'tempo_preparo' => $produto['tempo_preparo_min'] ? (int)$produto['tempo_preparo_min'] : null,
                        'disponivel' => (bool)$produto['disponivel'],
                        'destaque' => (bool)$produto['destaque'],
                        'vendas_hoje' => (int)$produto['vendas_hoje'],
                        'nota_media' => (float)$produto['nota_media'],
                        'total_avaliacoes' => (int)$produto['total_avaliacoes'],
                        'subcategoria_id' => $produto['subcategoria_id'] ? (int)$produto['subcategoria_id'] : null,
                        'ingredientes' => $produto['ingredientes'] ? json_decode($produto['ingredientes'], true) : null,
                        'opcionais' => $produto['opcoes'] ? json_decode($produto['opcoes'], true) : null,
                    ];
                }, $produtosDaSecao),
            ];
        }
        
        // Ordena as seções pela ordem definida
        usort($secoes, function ($a, $b) {
            return $a['ordem'] <=> $b['ordem'];
        });
        
        $totalPages = ceil($totalProdutosGeral / $perPage);
        
        return [
            'secoes' => $secoes,
            'pagination' => [
                'total' => $totalProdutosGeral,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
            ],
        ];
    }
    
    /**
     * Retorna a cláusula ORDER BY
     */
    private function getOrderBySql($orderBy)
    {
        switch ($orderBy) {
            case 'destaque':
                return "ORDER BY p.destaque DESC, p.nome ASC";
            case 'preco_asc':
                return "ORDER BY p.preco ASC, p.nome ASC";
            case 'preco_desc':
                return "ORDER BY p.preco DESC, p.nome ASC";
            case 'mais_pedidos':
                return "ORDER BY p.vendas_hoje DESC, p.destaque DESC, p.nome ASC";
            case 'avaliacao':
                return "ORDER BY p.nota_media DESC, p.total_avaliacoes DESC, p.nome ASC";
            case 'relevancia':
            default:
                return "ORDER BY p.destaque DESC, p.vendas_hoje DESC, p.nota_media DESC, p.nome ASC";
        }
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
    
    /**
     * Obtém as opções de filtro
     */
    private function getFilterOptions($lojaId)
    {
        $sql = "SELECT subcategoria.id, subcategoria.nome, subcategoria.icone, subcategoria.ordem,
                       COUNT(produto.id) as total_produtos
                FROM subcategoria
                INNER JOIN produto ON produto.subcategoria_id = subcategoria.id
                WHERE produto.loja_id = :loja_id
                    AND produto.deletado_em IS NULL
                    AND produto.ativo = 1
                    AND produto.disponivel = 1
                GROUP BY subcategoria.id
                ORDER BY subcategoria.ordem ASC";
        
        $categorias = Yii::$app->db->createCommand($sql, [':loja_id' => $lojaId])->queryAll();
        
        $categoriasOptions = [];
        foreach ($categorias as $cat) {
            $categoriasOptions[] = [
                'id' => (int)$cat['id'],
                'nome' => $cat['nome'],
                'icone' => $cat['icone'] ?: $this->getIconePorNome($cat['nome']),
                'total_produtos' => (int)$cat['total_produtos'],
            ];
        }
        
        $ordenacaoOptions = [
            ['value' => 'relevancia', 'label' => 'Relevância', 'icon' => '⭐'],
            ['value' => 'destaque', 'label' => 'Destaques', 'icon' => '🔥'],
            ['value' => 'preco_asc', 'label' => 'Menor Preço', 'icon' => '💰'],
            ['value' => 'preco_desc', 'label' => 'Maior Preço', 'icon' => '💸'],
            ['value' => 'mais_pedidos', 'label' => 'Mais Pedidos', 'icon' => '📊'],
            ['value' => 'avaliacao', 'label' => 'Melhor Avaliados', 'icon' => '⭐'],
        ];
        
        return [
            'categorias' => $categoriasOptions,
            'ordenacao' => $ordenacaoOptions,
        ];
    }
    
    /**
     * Obtém as opções de meio a meio para a loja
     * 
     * @param int $lojaId
     * @param array|string $config
     * @return array|null
     */
    private function getMeioAMeioOptions($lojaId, $config)
    {
        // Se a configuração é uma string, tenta decodificar
        if (is_string($config)) {
            $config = json_decode($config, true);
            if (!is_array($config)) {
                $config = [];
            }
        }
        
        // Configurações padrão
        $defaultOptions = [
            'ativo' => true,
            'max_sabores' => 2,
            'regra_preco' => 'maior',
            'descricao_regra' => $this->getDescricaoRegra('maior'),
            'sabores_disponiveis' => [],
        ];
        
        // Mescla com as configurações existentes
        $options = array_merge($defaultOptions, $config);
        
        // Se não tem sabores definidos, busca do banco
        if (empty($options['sabores_disponiveis'])) {
            $sql = "SELECT p.id, p.nome, p.preco, p.imagem
                    FROM produto p
                    WHERE p.loja_id = :loja_id
                        AND p.deletado_em IS NULL
                        AND p.ativo = 1
                        AND p.disponivel = 1
                        AND p.tipo IN ('pizza', 'sabor')
                    ORDER BY p.nome ASC";
            
            $sabores = Yii::$app->db->createCommand($sql, [':loja_id' => $lojaId])->queryAll();
            
            $options['sabores_disponiveis'] = array_map(function($sabor) {
                return [
                    'id' => (int)$sabor['id'],
                    'nome' => $sabor['nome'],
                    'preco' => (float)$sabor['preco'],
                    'imagem' => $sabor['imagem'],
                ];
            }, $sabores);
        }
        
        // Atualiza a descrição da regra
        if (isset($options['regra_preco'])) {
            $options['descricao_regra'] = $this->getDescricaoRegra($options['regra_preco']);
        }
        
        return $options;
    }
    
    private function getIconePorNome($nome)
    {
        $icones = [
            'Pizzas Salgadas' => '🍕',
            'Pizzas Doces' => '🍫',
            'Meio a Meio' => '🤝',
            'Bordas Recheadas' => '🧀',
            'Calzones' => '🥟',
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