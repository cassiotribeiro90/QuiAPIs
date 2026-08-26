<?php
// controllers/api/gestor/LojaController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\Loja;
use app\controllers\api\gestor\ControllerBase;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\caching\DbDependency;
use app\models\api\gestor\Produto;

class LojaController extends ControllerBase
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        try {
            $this->getUserByToken();

            $request = Yii::$app->request;

            $query = Loja::find()
                ->where(['deletado_em' => null])
                ->orderBy(['criado_em' => SORT_DESC]);

            // ===== FILTROS =====
            if ($request->get('status')) {
                $statusList = explode(',', $request->get('status'));
                $statusList = array_map('trim', $statusList);
                $statusList = array_filter($statusList);
                if (!empty($statusList)) {
                    $query->andWhere(['in', 'status', $statusList]);
                }
            }

            if ($request->get('categorias')) {
                $categoriaList = explode(',', $request->get('categorias'));
                $categoriaList = array_map('trim', $categoriaList);
                $categoriaList = array_filter($categoriaList);
                if (!empty($categoriaList)) {
                    $query->andWhere(['in', 'categoria', $categoriaList]);
                }
            }

            if ($request->get('destaque') !== null) {
                $query->andWhere(['destaque' => (int)$request->get('destaque')]);
            }

            if ($request->get('verificado') !== null) {
                $query->andWhere(['verificado' => (int)$request->get('verificado')]);
            }

            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'nome', $search],
                    ['like', 'descricao', $search],
                    ['like', 'cidade', $search],
                ]);
            }

            // ===== FILTER OPTIONS COM CACHE =====
            $filterOptions = $this->generateFilterOptions();

            // ===== PAGINAÇÃO =====
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $lojas = $query->offset($offset)->limit($perPage)->all();

            $data = array_map(function($loja) {
                return $this->formatarLoja($loja);
            }, $lojas);

            return ApiResponse::success([
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ],
                'filter_options' => $filterOptions,
                'items' => $data,
            ], 'Lista de lojas recuperada com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                $e instanceof \yii\web\UnauthorizedHttpException ? 'unauthorized' : 'internal_error'
            );
        }
    }

    /**
     * GET /api/gestor/lojas/<id>
     * Visualiza uma loja específica
     */
    public function actionView($id)
    {
        try {
            $this->getUserByToken();
            $loja = $this->findModel($id);

            return ApiResponse::success(
                $this->formatarLoja($loja, true),
                'Loja encontrada com sucesso'
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 404,
                'not_found'
            );
        }
    }

    /**
     * POST /api/gestor/lojas
     * Cria uma nova loja
     */
    public function actionCreate()
    {
        try {
            $this->getUserByToken();
            /** @var \app\models\api\gestor\Gestor $gestor */
            $dados = Yii::$app->request->post();

            // Valida campos obrigatórios
            $obrigatorios = ['nome', 'categoria', 'tempo_entrega_min', 'tempo_entrega_max', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'telefone'];
            $erros = [];
            foreach ($obrigatorios as $campo) {
                if (empty($dados[$campo])) {
                    $erros[$campo][] = "$campo é obrigatório";
                }
            }
            if (!empty($erros)) {
                return ApiResponse::error(
                    'Campos obrigatórios não preenchidos',
                    400,
                    'missing_fields',
                    $erros
                );
            }

            // Validações de formato
            if (!empty($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                return ApiResponse::error(
                    'E-mail inválido',
                    400,
                    'invalid_email',
                    ['email' => ['Formato de e-mail inválido']]
                );
            }

            // Verifica duplicidade de nome
            $existe = Loja::find()->where(['nome' => $dados['nome']])->exists();
            if ($existe) {
                return ApiResponse::error(
                    'Já existe uma loja com este nome',
                    409,
                    'duplicate_name',
                    ['nome' => ['Nome já está em uso']]
                );
            }

            $loja = new Loja();
            $this->popularLoja($loja, $dados);

            // Valores padrão
            $loja->status = $dados['status'] ?? Loja::STATUS_ATIVO;
            $loja->fluxo_status = $dados['fluxo_status'] ?? Loja::FLUXO_NORMAL;
            $loja->cor_tema = $dados['cor_tema'] ?? '#FF6B6B';
            $loja->verificado = $dados['verificado'] ?? 0;
            $loja->destaque = $dados['destaque'] ?? 0;
            $loja->trending_score = $dados['trending_score'] ?? 0;

            if ($loja->save()) {
                return ApiResponse::success(
                    $this->formatarLoja($loja, true),
                    'Loja criada com sucesso',
                    201
                );
            }

            return ApiResponse::error(
                'Erro ao criar loja',
                422,
                'validation_failed',
                $loja->errors
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro interno: ' . $e->getMessage(),
                500,
                'internal_error'
            );
        }
    }

    /**
     * PUT /api/gestor/lojas/<id>
     * Atualiza uma loja
     */
    public function actionUpdate($id)
    {
        try {
            $this->getUserByToken();
            $loja = $this->findModel($id);

            $dados = Yii::$app->request->post();

            // Se nome foi alterado, verificar duplicidade
            if (isset($dados['nome']) && $dados['nome'] !== $loja->nome) {
                $existe = Loja::find()->where(['nome' => $dados['nome']])->andWhere(['!=', 'id', $id])->exists();
                if ($existe) {
                    return ApiResponse::error(
                        'Já existe uma loja com este nome',
                        409,
                        'duplicate_name',
                        ['nome' => ['Nome já está em uso']]
                    );
                }
            }

            // Valida email se alterado
            if (isset($dados['email']) && $dados['email'] !== $loja->email) {
                if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                    return ApiResponse::error(
                        'E-mail inválido',
                        400,
                        'invalid_email',
                        ['email' => ['Formato de e-mail inválido']]
                    );
                }
            }

            $this->popularLoja($loja, $dados);

            if ($loja->save()) {
                return ApiResponse::success(
                    $this->formatarLoja($loja, true),
                    'Loja atualizada com sucesso'
                );
            }

            return ApiResponse::error(
                'Erro ao atualizar loja',
                422,
                'validation_failed',
                $loja->errors
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'internal_error'
            );
        }
    }

    /**
     * DELETE /api/gestor/lojas/<id>
     * Remove (soft delete) uma loja
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();

            // Apenas admin pode deletar
            if ($usuarioLogado->nivel !== 'admin') {
                return ApiResponse::error(
                    'Apenas administradores podem remover lojas',
                    403,
                    'forbidden'
                );
            }

            $loja = $this->findModel($id);
            
            if ($loja->softDelete()) {
                return ApiResponse::success(null, 'Loja removida com sucesso');
            }

            return ApiResponse::error(
                'Erro ao remover loja',
                500,
                'delete_failed'
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'internal_error'
            );
        }
    }

    /**
     * GET /api/gestor/lojas/<id>/produtos
     * Lista todos os produtos de uma loja específica
     */
    public function actionProdutos($id)
    {
        try {
            $this->getUserByToken();
            
            // Verifica se a loja existe
            $loja = $this->findModel($id);
            
            $request = Yii::$app->request;
            
            // Query base com relacionamentos
            $query = Produto::find()
                ->where(['loja_id' => $id])
                ->andWhere(['deletado_em' => null])
                ->with(['subcategoria.categoria']) // Carrega subcategoria e sua categoria
                ->orderBy(['ordem' => SORT_ASC, 'nome' => SORT_ASC]);
            
            // ===== FILTROS =====
            if ($request->get('categoria_id')) {
                $categoriaList = array_map('intval', explode(',', $request->get('categoria_id')));
                $categoriaList = array_filter($categoriaList);
                if (!empty($categoriaList)) {
                    $query->joinWith(['subcategoria']);
                    $query->andWhere(['in', 'subcategoria.categoria_id', $categoriaList]);
                }
            }
            
            if ($request->get('subcategoria_id')) {
                $subcategoriaList = array_map('intval', explode(',', $request->get('subcategoria_id')));
                $subcategoriaList = array_filter($subcategoriaList);
                if (!empty($subcategoriaList)) {
                    $query->andWhere(['in', 'subcategoria_id', $subcategoriaList]);
                }
            }
            
            if ($request->get('disponivel') !== null) {
                $query->andWhere(['disponivel' => (int)$request->get('disponivel')]);
            }
            
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'nome', $search],
                    ['like', 'descricao', $search],
                ]);
            }
            
            // ===== PAGINAÇÃO =====
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;
            
            $total = $query->count();
            $produtos = $query->offset($offset)->limit($perPage)->all();
            
            // ===== FORMATAR E AGRUPAR PRODUTOS =====
            $produtosPorCategoria = [];
            $categoriasComContagem = [];
            
            foreach ($produtos as $produto) {
                $produtoFormatado = $this->formatarProduto($produto);
                $categoriaNome = $produtoFormatado['categoria_nome'] ?? 'Outros';
                
                if (!isset($produtosPorCategoria[$categoriaNome])) {
                    $produtosPorCategoria[$categoriaNome] = [];
                    $categoriasComContagem[$categoriaNome] = 0;
                }
                
                $produtosPorCategoria[$categoriaNome][] = $produtoFormatado;
                $categoriasComContagem[$categoriaNome]++;
            }
            
            // Coloca "Outros" por último
            if (isset($produtosPorCategoria['Outros'])) {
                $outros = $produtosPorCategoria['Outros'];
                unset($produtosPorCategoria['Outros']);
                $produtosPorCategoria['Outros'] = $outros;
            }
            
            // ===== METADADOS DAS CATEGORIAS =====
            $categoriasMetadata = [];
            foreach ($categoriasComContagem as $nome => $count) {
                $categoriasMetadata[] = [
                    'nome' => $nome,
                    'count' => $count,
                ];
            }
            
            return ApiResponse::success([
                'items' => $produtosPorCategoria,
                'categories' => $categoriasMetadata,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ]
            ], 'Produtos da loja recuperados com sucesso');
            
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'internal_error'
            );
        }
    }

    /**
     * Formata dados do produto para resposta
     * Agora obtém categoria através da subcategoria (mais elegante)
     */
    private function formatarProduto($produto, $detalhado = false)
    {
        // Obtém categoria através da subcategoria (relacionamento já carregado)
        $categoria = $produto->subcategoria ? $produto->subcategoria->categoria : null;
        
        $dados = [
            'id' => $produto->id,
            'loja_id' => $produto->loja_id,
            'nome' => $produto->nome,
            'slug' => $produto->slug,
            'descricao' => $produto->descricao,
            'preco' => (float)$produto->preco,
            'preco_promocional' => $produto->preco_promocional ? (float)$produto->preco_promocional : null,
            'imagem' => $produto->imagem,
            
            // Informações da categoria (via subcategoria)
            'categoria_id' => $categoria ? $categoria->id : null,
            'categoria_nome' => $categoria ? $categoria->nome : 'Outros',
            'categoria_icone' => $categoria ? $categoria->icone : null,
            
            // Informações da subcategoria
            'subcategoria_id' => $produto->subcategoria_id,
            'subcategoria_nome' => $produto->subcategoria ? $produto->subcategoria->nome : null,
            
            // Status
            'disponivel' => (bool)$produto->disponivel,
            'ativo' => (bool)$produto->ativo,
            'destaque' => (bool)$produto->destaque,
            
            // Outros campos
            'tempo_preparo_min' => $produto->tempo_preparo_min,
            'ordem' => (int)$produto->ordem,
            'criado_em' => $produto->criado_em,
        ];
        
        if ($detalhado) {
            $dados = array_merge($dados, [
                'imagens' => $produto->imagens,
                'ingredientes' => $produto->ingredientes,
                'ingredientes_texto' => $produto->ingredientes_texto,
                'calorias' => $produto->calorias,
                'peso_gramas' => $produto->peso_gramas,
                'contem_gluten' => (bool)$produto->contem_gluten,
                'contem_lactose' => (bool)$produto->contem_lactose,
                'vegano' => (bool)$produto->vegano,
                'vegetariano' => (bool)$produto->vegetariano,
                'apimentado' => (bool)$produto->apimentado,
                'selos' => $produto->selos,
                'disponivel_inicio' => $produto->disponivel_inicio,
                'disponivel_fim' => $produto->disponivel_fim,
                'disponivel_dias' => $produto->disponivel_dias,
                'variacoes' => $produto->variacoes,
                'opcoes' => $produto->opcoes,
                'estoque' => $produto->estoque,
                'nota_media' => (float)$produto->nota_media,
                'total_avaliacoes' => (int)$produto->total_avaliacoes,
                'atualizado_em' => $produto->atualizado_em,
            ]);
        }
        
        return $dados;
    }

    /**
     * GET /api/gestor/lojas/options
     * Retorna opções para selects (categorias, status, etc)
     */
    public function actionOptions()
    {
        try {
            $this->getUserByToken();

            // Buscar categorias distintas existentes no banco
            $categorias = Loja::find()
                ->select('categoria')
                ->distinct()
                ->where(['deletado_em' => null])
                ->column();

            return ApiResponse::success([
                'categorias' => $categorias,
                'status' => [
                    ['value' => Loja::STATUS_ATIVO, 'label' => 'Ativo'],
                    ['value' => Loja::STATUS_INATIVO, 'label' => 'Inativo'],
                    ['value' => Loja::STATUS_FECHADO, 'label' => 'Fechado'],
                    ['value' => Loja::STATUS_REVISAO, 'label' => 'Revisão'],
                ],
                'fluxo_status' => [
                    ['value' => Loja::FLUXO_VAZIO, 'label' => 'Vazio'],
                    ['value' => Loja::FLUXO_NORMAL, 'label' => 'Normal'],
                    ['value' => Loja::FLUXO_CHEIO, 'label' => 'Cheio'],
                    ['value' => Loja::FLUXO_LOTADO, 'label' => 'Lotado'],
                ]
            ], 'Opções recuperadas com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 401,
                'unauthorized'
            );
        }
    }

    /**
     * Busca model pelo ID
     */
    private function findModel($id)
    {
        $loja = Loja::find()->where(['id' => $id])->one();
        if (!$loja) {
            throw new NotFoundHttpException('Loja não encontrada');
        }
        return $loja;
    }

    /**
     * Formata dados da loja para resposta
     */
    private function formatarLoja($loja, $detalhado = false)
    {
        $dados = [
            'id' => $loja->id,
            'nome' => $loja->nome,
            'slug' => $loja->slug,
            'categoria' => $loja->categoria,
            'logo' => $loja->logo,
            'capa' => $loja->capa,
            'nota_media' => (float)$loja->nota_media,
            'total_avaliacoes' => (int)$loja->total_avaliacoes,
            'tempo_entrega_min' => (int)$loja->tempo_entrega_min,
            'tempo_entrega_max' => (int)$loja->tempo_entrega_max,
            'taxa_entrega' => (float)$loja->taxa_entrega,
            'pedido_minimo' => (float)$loja->pedido_minimo,
            'cidade' => $loja->cidade,
            'uf' => $loja->uf,
            'status' => $loja->status,
            'verificado' => (bool)$loja->verificado,
            'destaque' => (bool)$loja->destaque,
            'criado_em' => $loja->criado_em,
        ];

        if ($detalhado) {
            $dados = array_merge($dados, [
                'descricao' => $loja->descricao,
                'cep' => $loja->cep,
                'logradouro' => $loja->logradouro,
                'numero' => $loja->numero,
                'complemento' => $loja->complemento,
                'bairro' => $loja->bairro,
                'latitude' => $loja->latitude,
                'longitude' => $loja->longitude,
                'telefone' => $loja->telefone,
                'whatsapp' => $loja->whatsapp,
                'email' => $loja->email,
                'instagram' => $loja->instagram,
                'fluxo_status' => $loja->fluxo_status,
                'cor_tema' => $loja->cor_tema,
                'configuracoes' => $loja->configuracoes,
                'atualizado_em' => $loja->atualizado_em,
            ]);
        }

        return $dados;
    }

    /**
     * Popula dados da loja
     */
    private function popularLoja($loja, $dados)
    {
        $campos = [
            'nome', 'descricao', 'categoria', 'logo', 'capa',
            'tempo_entrega_min', 'tempo_entrega_max', 'taxa_entrega', 'pedido_minimo',
            'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf',
            'latitude', 'longitude', 'telefone', 'whatsapp', 'email', 'instagram',
            'status', 'verificado', 'destaque', 'trending_score', 'fluxo_status',
            'cor_tema', 'configuracoes'
        ];

        foreach ($campos as $campo) {
            if (isset($dados[$campo])) {
                $loja->$campo = $dados[$campo];
            }
        }
    }

    /**
     * Gera as opções de filtro com cache
     */
    private function generateFilterOptions()
    {
        $cacheKey = 'lojas_filter_options_v2';
        
        $dependency = new DbDependency([
            'sql' => 'SELECT MAX(atualizado_em) FROM loja WHERE deletado_em IS NULL',
        ]);
        
        $filterOptions = Yii::$app->cache->getOrSet(
            $cacheKey,
            function () {
                Yii::info("Gerando filterOptions (cache expirado)", __METHOD__);
                return $this->buildFilterOptions();
            },
            3600,
            $dependency
        );
        
        return $filterOptions;
    }

    /**
     * Constrói as opções de filtro (sem cache)
     */
    private function buildFilterOptions()
    {
        // Categorias com pelo menos 1 loja
        $categoriasComLojas = (new \yii\db\Query())
            ->select(['categoria', 'COUNT(*) as total'])
            ->from('loja')
            ->where(['deletado_em' => null])
            ->andWhere(['is not', 'categoria', null])
            ->andWhere(['<>', 'categoria', ''])
            ->groupBy('categoria')
            ->having(['>', 'total', 0])
            ->orderBy(['total' => SORT_DESC])
            ->all();

        $categoriaOptions = [];
        foreach ($categoriasComLojas as $item) {
            $categoriaOptions[] = [
                'value' => $item['categoria'],
                'label' => $item['categoria'],
                'count' => (int)$item['total'],
            ];
        }

        // Status com contagens
        $statusCounts = (new \yii\db\Query())
            ->select(['status', 'COUNT(*) as total'])
            ->from('loja')
            ->where(['deletado_em' => null])
            ->groupBy('status')
            ->all();

        $statusLabels = [
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            'fechado' => 'Fechado',
            'revisao' => 'Revisão',
        ];

        $statusOptions = [];
        foreach ($statusCounts as $item) {
            $statusOptions[] = [
                'value' => $item['status'],
                'label' => $statusLabels[$item['status']] ?? ucfirst($item['status']),
                'count' => (int)$item['total'],
            ];
        }

        // Flags
        $destaqueCount = Loja::find()
            ->where(['deletado_em' => null, 'destaque' => 1])
            ->count();

        $verificadoCount = Loja::find()
            ->where(['deletado_em' => null, 'verificado' => 1])
            ->count();

        return [
            'status' => $statusOptions,
            'categorias' => $categoriaOptions,
            'destaque' => (int)$destaqueCount,
            'verificado' => (int)$verificadoCount,
        ];
    }
}