<?php
// controllers/api/gestor/CategoriaController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\Categoria;
use app\controllers\api\gestor\ControllerBase;
use yii\web\NotFoundHttpException;
use yii\caching\DbDependency;

class CategoriaController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/categorias
     * Lista todas as categorias com paginação, filtros e opções de filtro
     */
    public function actionIndex()
    {
        try {
            $this->getUserByToken();

            $request = Yii::$app->request;

            $query = Categoria::find()
                ->orderBy(['ordem' => SORT_ASC, 'nome' => SORT_ASC]);

            // Filtros (usando 'ativo', não 'deletado_em')
            if ($request->get('ativo') !== null) {
                $query->andWhere(['ativo' => (int)$request->get('ativo')]);
            }
            if ($request->get('destaque') !== null) {
                $query->andWhere(['destaque' => (int)$request->get('destaque')]);
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
            $categorias = $query->offset($offset)->limit($perPage)->all();

            $data = array_map(function($categoria) {
                return $this->formatarCategoria($categoria);
            }, $categorias);

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
            ], 'Lista de categorias recuperada com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'internal_error'
            );
        }
    }

    /**
     * GET /api/gestor/categorias/<id>
     */
    public function actionView($id)
    {
        try {
            $this->getUserByToken();
            $categoria = $this->findModel($id);

            return ApiResponse::success(
                $this->formatarCategoria($categoria, true),
                'Categoria encontrada com sucesso'
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
     * POST /api/gestor/categorias/create
     */
    public function actionCreate()
    {
        try {
            $this->getUserByToken();

            $dados = Yii::$app->request->post();

            if (empty($dados['nome'])) {
                return ApiResponse::error(
                    'Nome é obrigatório',
                    400,
                    'missing_fields',
                    ['nome' => ['Nome é obrigatório']]
                );
            }

            if (Categoria::find()->where(['nome' => $dados['nome']])->exists()) {
                return ApiResponse::error(
                    'Já existe uma categoria com este nome',
                    409,
                    'duplicate_name',
                    ['nome' => ['Nome já está em uso']]
                );
            }

            $categoria = new Categoria();
            $this->popularCategoria($categoria, $dados);

            if ($categoria->save()) {
                return ApiResponse::success(
                    $this->formatarCategoria($categoria, true),
                    'Categoria criada com sucesso',
                    201
                );
            }

            return ApiResponse::error(
                'Erro ao criar categoria',
                422,
                'validation_failed',
                $categoria->errors
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
     * PUT /api/gestor/categorias/update/<id>
     */
    public function actionUpdate($id)
    {
        try {
            $this->getUserByToken();
            $categoria = $this->findModel($id);

            $dados = Yii::$app->request->post();

            if (isset($dados['nome']) && $dados['nome'] !== $categoria->nome) {
                $existe = Categoria::find()
                    ->where(['nome' => $dados['nome']])
                    ->andWhere(['!=', 'id', $id])
                    ->exists();
                if ($existe) {
                    return ApiResponse::error(
                        'Já existe uma categoria com este nome',
                        409,
                        'duplicate_name',
                        ['nome' => ['Nome já está em uso']]
                    );
                }
            }

            $this->popularCategoria($categoria, $dados);

            if ($categoria->save()) {
                return ApiResponse::success(
                    $this->formatarCategoria($categoria, true),
                    'Categoria atualizada com sucesso'
                );
            }

            return ApiResponse::error(
                'Erro ao atualizar categoria',
                422,
                'validation_failed',
                $categoria->errors
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
     * DELETE /api/gestor/categorias/delete/<id>
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();

            if ($usuarioLogado->nivel !== 'admin') {
                return ApiResponse::error(
                    'Apenas administradores podem remover categorias',
                    403,
                    'forbidden'
                );
            }

            $categoria = $this->findModel($id);

            // Verifica se tem subcategorias vinculadas
            if ($categoria->getSubcategorias()->count() > 0) {
                return ApiResponse::error(
                    'Não é possível excluir categoria com subcategorias vinculadas',
                    409,
                    'has_subcategorias'
                );
            }

            // Soft delete usando 'ativo' = 0 em vez de deletado_em
            $categoria->ativo = 0;
            if ($categoria->save()) {
                return ApiResponse::success(null, 'Categoria removida com sucesso');
            }

            return ApiResponse::error(
                'Erro ao remover categoria',
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
     * GET /api/gestor/categorias/options
     */
    public function actionOptions()
    {
        try {
            $this->getUserByToken();

            $categorias = Categoria::find()
                ->select(['id', 'nome', 'cor', 'icone'])
                ->where(['ativo' => 1])
                ->orderBy(['nome' => SORT_ASC])
                ->asArray()
                ->all();

            return ApiResponse::success([
                'categorias' => $categorias
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
        $categoria = Categoria::findOne($id);
        if (!$categoria) {
            throw new NotFoundHttpException('Categoria não encontrada');
        }
        return $categoria;
    }

    private function formatarCategoria($categoria, $detalhado = false)
    {
        $dados = [
            'id' => $categoria->id,
            'nome' => $categoria->nome,
            'slug' => $categoria->slug,
            'icone' => $categoria->icone,
            'cor' => $categoria->cor,
            'ativo' => (bool)$categoria->ativo,
            'destaque' => (bool)$categoria->destaque,
            'ordem' => (int)$categoria->ordem,
        ];

        if ($detalhado) {
            $dados = array_merge($dados, [
                'descricao' => $categoria->descricao,
                'imagem' => $categoria->imagem,
                'criado_em' => $categoria->criado_em,
                'atualizado_em' => $categoria->atualizado_em,
                'total_subcategorias' => $categoria->getSubcategorias()->count(),
            ]);
        }

        return $dados;
    }

    private function popularCategoria($categoria, $dados)
    {
        $campos = ['nome', 'descricao', 'icone', 'imagem', 'cor', 'ordem', 'ativo', 'destaque'];
        foreach ($campos as $campo) {
            if (isset($dados[$campo])) {
                $categoria->$campo = $dados[$campo];
            }
        }
    }

    /**
     * 🔥 Gera as opções de filtro (com cache)
     */
    private function generateFilterOptions()
    {
        $cacheKey = 'categorias_filter_options_v2';
        
        $dependency = new DbDependency([
            'sql' => 'SELECT MAX(atualizado_em) FROM categoria',
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
        // Contagem de categorias ativas e inativas
        $ativoCount = Categoria::find()->where(['ativo' => 1])->count();
        $inativoCount = Categoria::find()->where(['ativo' => 0])->count();

        // Destaque (apenas ativas)
        $destaqueCount = Categoria::find()
            ->where(['ativo' => 1, 'destaque' => 1])
            ->count();

        return [
            'ativo' => [
                [
                    'value' => '1',
                    'label' => 'Ativos',
                    'count' => (int)$ativoCount,
                ],
                [
                    'value' => '0',
                    'label' => 'Inativos',
                    'count' => (int)$inativoCount,
                ],
            ],
            'destaque' => [
                [
                    'value' => '1',
                    'label' => 'Destaque',
                    'count' => (int)$destaqueCount,
                ],
            ],
        ];
    }
}