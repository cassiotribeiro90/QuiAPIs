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
     * Retorna dados da loja, seções com produtos paginados,
     * opções de filtro e configurações.
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
            $lojaId = (int)$id;

            if (!$lojaId) {
                return ApiResponse::error('ID da loja não informado', 400);
            }

            $categoriaId = $request->get('categoria_id') ? (int)$request->get('categoria_id') : null;
            $search = $request->get('search', '');
            $orderBy = $request->get('order_by', 'relevancia');
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);

            $loja = Loja::find()
                ->where(['id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$loja) {
                return ApiResponse::error('Loja não encontrada', 404);
            }

            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $distancia = null;
            if ($latitude && $longitude && $loja->latitude && $loja->longitude) {
                $distancia = DistanceCalculator::euclidean(
                    (float)$latitude,
                    (float)$longitude,
                    (float)$loja->latitude,
                    (float)$loja->longitude
                );
            }

            $secoesData = $this->getSecoesComProdutos(
                $lojaId,
                $categoriaId,
                $search,
                $orderBy,
                $page,
                $perPage
            );

            $filterOptions = $this->getFilterOptions($lojaId);

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

            $meioAMeio = null;
            if (isset($configuracoes['meio_a_meio']) && $configuracoes['meio_a_meio']) {
                $meioAMeio = $this->getMeioAMeioOptions($lojaId, $configuracoes['meio_a_meio']);
            }

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
                'status' => $loja->status,
                'fluxo_status' => $loja->fluxo_status,
                'cor_tema' => $loja->cor_tema,
                'configuracoes' => $configuracoes,
                'meio_a_meio' => $meioAMeio,
                'secoes' => $secoesData['secoes'],
                'pagination' => $secoesData['pagination'],
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

            $data = array_map(function ($secao) {
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
     * Busca produtos agrupados por seções (subcategorias) com paginação.
     * A busca filtra produtos pelo nome, descrição, ingredientes e subcategoria,
     * com correspondência aproximada (Levenshtein).
     *
     * Todas as seções da loja são retornadas, mesmo que não tenham produtos na página atual.
     */
    private function getSecoesComProdutos($lojaId, $categoriaId, $search, $orderBy, $page, $perPage)
    {
        $params = [':loja_id' => $lojaId];

        // Condições base para produtos (sem filtro de busca)
        $whereProduto = [
            "p.loja_id = :loja_id",
            "p.deletado_em IS NULL",
            "p.ativo = 1",
            "p.disponivel = 1"
        ];

        if ($categoriaId && $categoriaId > 0) {
            $whereProduto[] = "p.subcategoria_id = :categoria_id";
            $params[':categoria_id'] = $categoriaId;
        }

        $whereClause = implode(" AND ", $whereProduto);
        $orderSql = $this->getOrderBySql($orderBy);

        // Consulta todos os produtos da loja (sem filtro de busca) para aplicar a busca em PHP
        $sqlProdutos = "SELECT p.*, s.id AS secao_id, s.nome AS secao_nome,
                            s.icone AS secao_icone, s.ordem AS secao_ordem
                        FROM produto p
                        INNER JOIN subcategoria s ON p.subcategoria_id = s.id
                        WHERE {$whereClause}
                        {$orderSql}";

        $todosProdutos = Yii::$app->db->createCommand($sqlProdutos, $params)->queryAll();

        // Aplica filtro de busca aproximada
        if (!empty($search) && trim($search) !== '') {
            $termo = trim($search);
            $todosProdutos = array_values(array_filter($todosProdutos, function ($produto) use ($termo) {
                return $this->produtoCorrespondeBusca($produto, $termo);
            }));
        }

        // Paginação global sobre a lista filtrada
        $totalProdutos = count($todosProdutos);
        $offset = ($page - 1) * $perPage;
        $produtosPaginados = array_slice($todosProdutos, $offset, $perPage);

        // Agrupa produtos paginados por seção
        $secoesAgrupadas = [];
        foreach ($produtosPaginados as $produto) {
            $secaoId = (int)$produto['secao_id'];
            if (!isset($secoesAgrupadas[$secaoId])) {
                $secoesAgrupadas[$secaoId] = [
                    'id' => $secaoId,
                    'nome' => $produto['secao_nome'],
                    'icone' => $produto['secao_icone'] ?: $this->getIconePorNome($produto['secao_nome']),
                    'ordem' => (int)$produto['secao_ordem'],
                    'total_produtos' => 0,
                    'produtos' => [],
                ];
            }
            $secoesAgrupadas[$secaoId]['produtos'][] = $produto;
        }

        // Busca TODAS as subcategorias da loja com seus totais reais
        $sqlTodasSecoes = "SELECT s.id, s.nome, s.icone, s.ordem,
                                (SELECT COUNT(*) FROM produto p
                                WHERE p.subcategoria_id = s.id
                                    AND p.loja_id = :loja_id
                                    AND p.deletado_em IS NULL
                                    AND p.ativo = 1
                                    AND p.disponivel = 1) as total_produtos
                        FROM subcategoria s
                        WHERE s.id IN (SELECT DISTINCT subcategoria_id FROM produto WHERE loja_id = :loja_id)
                        ORDER BY s.ordem ASC";

        $todasSecoes = Yii::$app->db->createCommand($sqlTodasSecoes, [':loja_id' => $lojaId])->queryAll();

        // Garante que todas as seções estejam presentes no array final
        foreach ($todasSecoes as $secaoInfo) {
            $secaoId = (int)$secaoInfo['id'];
            if (!isset($secoesAgrupadas[$secaoId])) {
                $secoesAgrupadas[$secaoId] = [
                    'id' => $secaoId,
                    'nome' => $secaoInfo['nome'],
                    'icone' => $secaoInfo['icone'] ?: $this->getIconePorNome($secaoInfo['nome']),
                    'ordem' => (int)$secaoInfo['ordem'],
                    'total_produtos' => (int)$secaoInfo['total_produtos'],
                    'produtos' => [],
                ];
            } else {
                // Atualiza o total real (em caso de filtros que alteram a contagem)
                // Mas se houver busca, o total da seção deve ser o total filtrado ou original?
                // Aqui usamos o total original para manter a barra de categorias correta.
                // Se desejar mostrar apenas o total filtrado, pode manter o que foi calculado.
                $secoesAgrupadas[$secaoId]['total_produtos'] = (int)$secaoInfo['total_produtos'];
            }
        }

        // Formata as seções para a resposta
        $secoes = [];
        foreach ($secoesAgrupadas as $secao) {
            $secoes[] = [
                'id' => $secao['id'],
                'nome' => $secao['nome'],
                'icone' => $secao['icone'],
                'ordem' => $secao['ordem'],
                'total_produtos' => $secao['total_produtos'],
                'produtos' => array_map(function ($produto) {
                    return $this->formatarProduto($produto);
                }, $secao['produtos']),
            ];
        }

        // Ordena as seções por ordem e id
        usort($secoes, function ($a, $b) {
            if ($a['ordem'] != $b['ordem']) {
                return $a['ordem'] <=> $b['ordem'];
            }
            return $a['id'] <=> $b['id'];
        });

        $totalPages = (int)ceil($totalProdutos / $perPage);

        return [
            'secoes' => $secoes,
            'pagination' => [
                'total' => $totalProdutos,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
            ],
        ];
    }

    /**
     * Verifica se um produto corresponde ao termo de busca usando:
     * 1. Substring direta (nome, descrição, ingredientes_texto, ingredientes JSON, subcategoria)
     * 2. Distância Levenshtein entre o termo e cada palavra
     */
    private function produtoCorrespondeBusca($produto, $termo)
    {
        $termoNormalizado = $this->normalizarTexto($termo);
        if (empty($termoNormalizado)) {
            return true;
        }

        $campos = [
            $produto['nome'] ?? '',
            $produto['descricao'] ?? '',
            $produto['ingredientes_texto'] ?? '',
            $produto['secao_nome'] ?? '', // 🔥 Nome da subcategoria
        ];

        if (!empty($produto['ingredientes'])) {
            $ingredientesDecoded = json_decode($produto['ingredientes'], true);
            if (is_array($ingredientesDecoded)) {
                $campos[] = $this->extrairTextoDeJson($ingredientesDecoded);
            } else {
                $campos[] = (string)$produto['ingredientes'];
            }
        }

        $maxDistance = max(1, intdiv(strlen($termoNormalizado), 4));

        foreach ($campos as $campo) {
            $campoNormalizado = $this->normalizarTexto($campo);
            if ($campoNormalizado === '') {
                continue;
            }

            if (mb_strpos($campoNormalizado, $termoNormalizado) !== false) {
                return true;
            }

            $palavras = preg_split('/\s+/', $campoNormalizado);
            foreach ($palavras as $palavra) {
                if (levenshtein($palavra, $termoNormalizado) <= $maxDistance) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Extrai texto de um array JSON (ingredientes, opções etc.) para busca.
     */
    private function extrairTextoDeJson($array)
    {
        $texto = '';
        array_walk_recursive($array, function ($valor) use (&$texto) {
            $texto .= ' ' . (string)$valor;
        });
        return $texto;
    }

    /**
     * Normaliza texto: minúsculas, sem acentos.
     */
    private function normalizarTexto($texto)
    {
        $comAcentos = ['á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç','ñ','ý','ÿ','Á','À','Â','Ã','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï','Ó','Ò','Ô','Õ','Ö','Ú','Ù','Û','Ü','Ç','Ñ','Ý','Ÿ'];
        $semAcentos = ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','n','y','y','A','A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','C','N','Y','Y'];
        $texto = str_replace($comAcentos, $semAcentos, $texto);
        return strtolower($texto);
    }

    /**
     * Formata um produto para a resposta da API.
     */
    private function formatarProduto($produto)
    {
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
    }

    /**
     * Retorna a cláusula ORDER BY.
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
     * Obtém as opções de filtro (categorias e ordenação).
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
     * Obtém as opções de meio a meio para a loja.
     */
    private function getMeioAMeioOptions($lojaId, $config)
    {
        if (is_string($config)) {
            $config = json_decode($config, true);
            if (!is_array($config)) {
                $config = [];
            }
        }

        $defaultOptions = [
            'ativo' => true,
            'max_sabores' => 2,
            'regra_preco' => 'maior',
            'descricao_regra' => $this->getDescricaoRegra('maior'),
            'sabores_disponiveis' => [],
        ];

        $options = array_merge($defaultOptions, $config);

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

            $options['sabores_disponiveis'] = array_map(function ($sabor) {
                return [
                    'id' => (int)$sabor['id'],
                    'nome' => $sabor['nome'],
                    'preco' => (float)$sabor['preco'],
                    'imagem' => $sabor['imagem'],
                ];
            }, $sabores);
        }

        if (isset($options['regra_preco'])) {
            $options['descricao_regra'] = $this->getDescricaoRegra($options['regra_preco']);
        }

        return $options;
    }

    /**
     * Retorna descrição da regra de preço.
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
     * Retorna um ícone padrão com base no nome da seção.
     */
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

    /**
     * Formata distância em texto amigável.
     */
    private function formatarDistancia($distancia)
    {
        if ($distancia < 1) {
            return round($distancia * 1000) . 'm';
        }
        return number_format($distancia, 1, ',', '.') . 'km';
    }
}