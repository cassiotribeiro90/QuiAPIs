<?php
// controllers/api/gestor/SubcategoriaController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\Subcategoria;
use app\models\api\gestor\Categoria;
use app\controllers\api\gestor\ControllerBase;
use yii\web\NotFoundHttpException;
use yii\caching\DbDependency;

class SubcategoriaController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/subcategorias
     * Lista todas as subcategorias com paginação, filtros e opções de filtro
     */
    public function actionIndex()
    {
        try {
            $this->getUserByToken();

            $request = Yii::$app->request;

            $query = Subcategoria::find()
                ->with('categoria')
                ->orderBy(['categoria_id' => SORT_ASC, 'nome' => SORT_ASC]);

            // Filtros (usando 'ativo', não 'deletado_em')
            if ($request->get('categoria_id')) {
                $query->andWhere(['categoria_id' => (int)$request->get('categoria_id')]);
            }
            if ($request->get('ativo') !== null) {
                $query->andWhere(['ativo' => (int)$request->get('ativo')]);
            }
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'nome', $search],
                    ['like', 'descricao', $search],
                ]);
            }

            // Paginação
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $subcategorias = $query->offset($offset)->limit($perPage)->all();

            $data = array_map(function($subcategoria) {
                return $this->formatarSubcategoria($subcategoria);
            }, $subcategorias);

            // 🔥 FILTER OPTIONS (com cache)
            $filterOptions = $this->generateFilterOptions();

            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ],
                'filter_options' => $filterOptions,
            ], 'Lista de subcategorias recuperada com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'internal_error'
            );
        }
    }

    /**
     * GET /api/gestor/subcategorias/<id>
     */
    public function actionView($id)
    {
        try {
            $this->getUserByToken();
            $subcategoria = $this->findModel($id);

            return ApiResponse::success(
                $this->formatarSubcategoria($subcategoria, true),
                'Subcategoria encontrada com sucesso'
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
     * POST /api/gestor/subcategorias/create
     */
    public function actionCreate()
    {
        try {
            $this->getUserByToken();

            $dados = Yii::$app->request->post();

            $obrigatorios = ['categoria_id', 'nome'];
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

            $categoria = Categoria::findOne($dados['categoria_id']);
            if (!$categoria) {
                return ApiResponse::error(
                    'Categoria não encontrada',
                    400,
                    'invalid_category'
                );
            }

            $existe = Subcategoria::find()
                ->where([
                    'categoria_id' => $dados['categoria_id'],
                    'nome' => $dados['nome']
                ])->exists();
            if ($existe) {
                return ApiResponse::error(
                    'Já existe uma subcategoria com este nome nesta categoria',
                    409,
                    'duplicate_name'
                );
            }

            $subcategoria = new Subcategoria();
            $this->popularSubcategoria($subcategoria, $dados);

            if ($subcategoria->save()) {
                $subcategoria = Subcategoria::find()
                    ->with('categoria')
                    ->andWhere(['id' => $subcategoria->id])
                    ->one();
                return ApiResponse::success(
                    $this->formatarSubcategoria($subcategoria, true),
                    'Subcategoria criada com sucesso',
                    201
                );
            }

            return ApiResponse::error(
                'Erro ao criar subcategoria',
                422,
                'validation_failed',
                $subcategoria->errors
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
     * PUT /api/gestor/subcategorias/update/<id>
     */
    public function actionUpdate($id)
    {
        try {
            $this->getUserByToken();
            $subcategoria = $this->findModel($id);

            $dados = Yii::$app->request->post();

            if (isset($dados['categoria_id']) && $dados['categoria_id'] != $subcategoria->categoria_id) {
                $categoria = Categoria::findOne($dados['categoria_id']);
                if (!$categoria) {
                    return ApiResponse::error(
                        'Categoria não encontrada',
                        400,
                        'invalid_category'
                    );
                }
            }

            if (isset($dados['nome']) && $dados['nome'] !== $subcategoria->nome) {
                $categoriaId = $dados['categoria_id'] ?? $subcategoria->categoria_id;
                $existe = Subcategoria::find()
                    ->where([
                        'categoria_id' => $categoriaId,
                        'nome' => $dados['nome']
                    ])
                    ->andWhere(['!=', 'id', $id])
                    ->exists();
                if ($existe) {
                    return ApiResponse::error(
                        'Já existe uma subcategoria com este nome nesta categoria',
                        409,
                        'duplicate_name'
                    );
                }
            }

            $this->popularSubcategoria($subcategoria, $dados);

            if ($subcategoria->save()) {
                $subcategoria = Subcategoria::find()
                    ->with('categoria')
                    ->andWhere(['id' => $subcategoria->id])
                    ->one();
                return ApiResponse::success(
                    $this->formatarSubcategoria($subcategoria, true),
                    'Subcategoria atualizada com sucesso'
                );
            }

            return ApiResponse::error(
                'Erro ao atualizar subcategoria',
                422,
                'validation_failed',
                $subcategoria->errors
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
     * GET /api/gestor/subcategorias/por-categoria/<id>
     */
    public function actionPorCategoria($id)
    {
        try {
            $this->getUserByToken();

            $categoria = Categoria::findOne($id);
            if (!$categoria) {
                throw new NotFoundHttpException('Categoria não encontrada');
            }

            $subcategorias = Subcategoria::find()
                ->with('categoria')
                ->where(['categoria_id' => $id, 'ativo' => 1])
                ->orderBy(['ordem' => SORT_ASC, 'nome' => SORT_ASC])
                ->all();

            $data = array_map(function($sub) {
                return [
                    'id' => $sub->id,
                    'nome' => $sub->nome,
                    'icone' => $sub->icone,
                    'ordem' => $sub->ordem,
                    'categoria_icone' => $sub->categoria->icone ?? null,
                ];
            }, $subcategorias);

            return ApiResponse::success($data, 'Subcategorias recuperadas com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'internal_error'
            );
        }
    }

    /**
     * DELETE /api/gestor/subcategorias/delete/<id>
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();

            if ($usuarioLogado->nivel !== 'admin') {
                return ApiResponse::error(
                    'Apenas administradores podem remover subcategorias',
                    403,
                    'forbidden'
                );
            }

            $subcategoria = $this->findModel($id);

            // Verifica se tem produtos vinculados (quando houver model Produto)
            // if ($subcategoria->getProdutos()->count() > 0) { ... }

            // Soft delete usando 'ativo' = 0 em vez de deletado_em
            $subcategoria->ativo = 0;
            if ($subcategoria->save()) {
                return ApiResponse::success(null, 'Subcategoria removida com sucesso');
            }

            return ApiResponse::error(
                'Erro ao remover subcategoria',
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
     * GET /api/gestor/subcategorias/options
     */
    public function actionOptions()
    {
        try {
            $this->getUserByToken();

            $categoriaId = Yii::$app->request->get('categoria_id');
            $query = Subcategoria::find()
                ->select(['id', 'nome', 'categoria_id'])
                ->where(['ativo' => 1])
                ->orderBy(['nome' => SORT_ASC]);

            if ($categoriaId) {
                $query->andWhere(['categoria_id' => $categoriaId]);
            }

            $subcategorias = $query->asArray()->all();

            return ApiResponse::success([
                'subcategorias' => $subcategorias
            ], 'Opções recuperadas com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 401,
                'unauthorized'
            );
        }
    }

    // ==================== MÉTODOS AUXILIARES ====================

    private function findModel($id)
    {
        $subcategoria = Subcategoria::find()
            ->with('categoria')
            ->andWhere(['id' => $id])
            ->one();
        if (!$subcategoria) {
            throw new NotFoundHttpException('Subcategoria não encontrada');
        }
        return $subcategoria;
    }

    private function formatarSubcategoria($subcategoria, $detalhado = false)
    {
        $dados = [
            'id' => $subcategoria->id,
            'categoria_id' => $subcategoria->categoria_id,
            'nome' => $subcategoria->nome,
            'slug' => $subcategoria->slug,
            'icone' => $subcategoria->icone,
            'ativo' => (bool)$subcategoria->ativo,
            'ordem' => (int)$subcategoria->ordem,
            'categoria_icone' => $subcategoria->categoria->icone ?? null,
        ];

        if ($detalhado) {
            $dados = array_merge($dados, [
                'descricao' => $subcategoria->descricao,
                'imagem' => $subcategoria->imagem,
                'criado_em' => $subcategoria->criado_em,
                'atualizado_em' => $subcategoria->atualizado_em,
                'categoria_nome' => $subcategoria->categoria->nome ?? null,
            ]);
        }

        return $dados;
    }

    private function popularSubcategoria($subcategoria, $dados)
    {
        $campos = ['categoria_id', 'nome', 'descricao', 'icone', 'imagem', 'ordem', 'ativo'];
        foreach ($campos as $campo) {
            if (isset($dados[$campo])) {
                $subcategoria->$campo = $dados[$campo];
            }
        }
    }

    /**
     * 🔥 Gera as opções de filtro (com cache)
     */
    private function generateFilterOptions()
    {
        $cacheKey = 'subcategorias_filter_options_v2';
        
        $dependency = new DbDependency([
            'sql' => 'SELECT MAX(atualizado_em) FROM subcategoria',
        ]);

        return Yii::$app->cache->getOrSet(
            $cacheKey,
            function () {
                return $this->buildFilterOptions();
            },
            3600,
            $dependency
        );
    }

    /**
     * 🔥 Constrói as opções de filtro (sem cache)
     * Usa 'ativo' em vez de 'deletado_em'
     */
    private function buildFilterOptions()
    {
        // 🔥 CATEGORIAS COM CONTAGEM DE SUBCATEGORIAS
        // Conta todas as subcategorias (ativas e inativas)
        $categoriasComContagem = (new \yii\db\Query())
            ->select(['c.id', 'c.nome', 'c.icone', 'COUNT(s.id) as total'])
            ->from('categoria c')
            ->leftJoin('subcategoria s', 's.categoria_id = c.id')
            ->where(['c.ativo' => 1]) // apenas categorias ativas
            ->groupBy('c.id')
            ->having(['>', 'total', 0])
            ->orderBy(['c.nome' => SORT_ASC])
            ->all();

        $categoriaOptions = [];
        foreach ($categoriasComContagem as $item) {
            $categoriaOptions[] = [
                'value' => (string)$item['id'],
                'label' => $item['nome'],
                'count' => (int)$item['total'],
                'icone' => $item['icone'] ?? null,
            ];
        }

        // Ativo / Inativo
        $ativoCount = Subcategoria::find()->where(['ativo' => 1])->count();
        $inativoCount = Subcategoria::find()->where(['ativo' => 0])->count();

        return [
            'categoria_id' => $categoriaOptions,
            'ativo' => [
                [
                    'value' => '1',
                    'label' => 'Ativas',
                    'count' => (int)$ativoCount,
                ],
                [
                    'value' => '0',
                    'label' => 'Inativas',
                    'count' => (int)$inativoCount,
                ],
            ],
        ];
    }
}