<?php
// controllers/api/gestor/CategoriaController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\Categoria;
use app\controllers\api\gestor\ControllerBase;
use yii\web\NotFoundHttpException;

class CategoriaController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/categorias
     * Lista todas as categorias com paginação e filtros
     */
    public function actionIndex()
    {
        try {
            $this->getUserByToken();

            $request = Yii::$app->request;

            $query = Categoria::find()
                ->orderBy(['ordem' => SORT_ASC, 'nome' => SORT_ASC]);

            // Filtros
            if ($request->get('ativo') !== null) {
                $query->andWhere(['ativo' => $request->get('ativo')]);
            }

            if ($request->get('destaque') !== null) {
                $query->andWhere(['destaque' => $request->get('destaque')]);
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

            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ]
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
     * Visualiza uma categoria específica
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
     * Cria uma nova categoria
     */
    public function actionCreate()
    {
        try {
            $this->getUserByToken();

            $dados = Yii::$app->request->post();

            // Valida campos obrigatórios
            if (empty($dados['nome'])) {
                return ApiResponse::error(
                    'Nome é obrigatório',
                    400,
                    'missing_fields',
                    ['nome' => ['Nome é obrigatório']]
                );
            }

            // Verifica duplicidade
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
     * Atualiza uma categoria
     */
    public function actionUpdate($id)
    {
        try {
            $this->getUserByToken();
            $categoria = $this->findModel($id);

            $dados = Yii::$app->request->post();

            // Se nome foi alterado, verificar duplicidade
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
     * Remove uma categoria (verifica se tem subcategorias vinculadas)
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();

            // Apenas admin pode deletar
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

            if ($categoria->delete()) {
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
     * Retorna opções para selects (categorias simplificadas)
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

    /**
     * Busca model pelo ID
     */
    private function findModel($id)
    {
        $categoria = Categoria::findOne($id);
        if (!$categoria) {
            throw new NotFoundHttpException('Categoria não encontrada');
        }
        return $categoria;
    }

    /**
     * Formata dados da categoria para resposta
     */
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

    /**
     * Popula dados da categoria
     */
    private function popularCategoria($categoria, $dados)
    {
        $campos = ['nome', 'descricao', 'icone', 'imagem', 'cor', 'ordem', 'ativo', 'destaque'];
        
        foreach ($campos as $campo) {
            if (isset($dados[$campo])) {
                $categoria->$campo = $dados[$campo];
            }
        }
    }
}