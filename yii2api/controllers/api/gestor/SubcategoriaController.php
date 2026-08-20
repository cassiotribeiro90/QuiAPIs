<?php
// controllers/api/gestor/SubcategoriaController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\Subcategoria;
use app\models\api\gestor\Categoria;
use app\controllers\api\gestor\ControllerBase;
use yii\web\NotFoundHttpException;

class SubcategoriaController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/subcategorias
     * Lista todas as subcategorias com paginação e filtros
     */
    public function actionIndex()
    {
        try {
            $this->getUserByToken();

            $request = Yii::$app->request;

            $query = Subcategoria::find()
                ->with('categoria')   // Carrega a relação categoria para obter o emoji
                ->orderBy(['categoria_id' => SORT_ASC, 'nome' => SORT_ASC]);

            // Filtros
            if ($request->get('categoria_id')) {
                $query->andWhere(['categoria_id' => $request->get('categoria_id')]);
            }

            if ($request->get('ativo') !== null) {
                $query->andWhere(['ativo' => $request->get('ativo')]);
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

            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ]
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
     * Visualiza uma subcategoria específica
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
     * Cria uma nova subcategoria
     */
    public function actionCreate()
    {
        try {
            $this->getUserByToken();

            $dados = Yii::$app->request->post();

            // Valida campos obrigatórios
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

            // Verifica se categoria existe
            $categoria = Categoria::findOne($dados['categoria_id']);
            if (!$categoria) {
                return ApiResponse::error(
                    'Categoria não encontrada',
                    400,
                    'invalid_category'
                );
            }

            // Verifica duplicidade dentro da mesma categoria
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
                // Recarrega com relação para retornar dados completos
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
     * Atualiza uma subcategoria
     */
    public function actionUpdate($id)
    {
        try {
            $this->getUserByToken();
            $subcategoria = $this->findModel($id);

            $dados = Yii::$app->request->post();

            // Se mudou de categoria, verifica se nova categoria existe
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

            // Se nome foi alterado, verificar duplicidade
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
                // Recarrega com relação para garantir dados atualizados
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
     * Retorna todas as subcategorias ativas de uma categoria específica
     */
    public function actionPorCategoria($id)
    {
        try {
            $this->getUserByToken();

            // Verifica se a categoria existe
            $categoria = Categoria::findOne($id);
            if (!$categoria) {
                throw new NotFoundHttpException('Categoria não encontrada');
            }

            $subcategorias = Subcategoria::find()
                ->with('categoria')   // carrega relação para obter emoji
                ->where(['categoria_id' => $id, 'ativo' => 1])
                ->orderBy(['ordem' => SORT_ASC, 'nome' => SORT_ASC])
                ->all();

            $data = array_map(function($sub) {
                return [
                    'id' => $sub->id,
                    'nome' => $sub->nome,
                    'icone' => $sub->icone,
                    'ordem' => $sub->ordem,
                    'categoria_icone' => $sub->categoria->icone ?? null, // novo campo
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
     * Remove uma subcategoria (verifica se tem produtos vinculados)
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();

            // Apenas admin pode deletar
            if ($usuarioLogado->nivel !== 'admin') {
                return ApiResponse::error(
                    'Apenas administradores podem remover subcategorias',
                    403,
                    'forbidden'
                );
            }

            $subcategoria = $this->findModel($id);

            // Verifica se tem produtos vinculados (quando existir model Produto)
            // if ($subcategoria->getProdutos()->count() > 0) {
            //     return ApiResponse::error(
            //         'Não é possível excluir subcategoria com produtos vinculados',
            //         409,
            //         'has_products'
            //     );
            // }

            if ($subcategoria->delete()) {
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
     * Retorna opções para selects
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

    /**
     * Busca model pelo ID (carregando a relação categoria)
     */
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

    /**
     * Formata dados da subcategoria para resposta
     */
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
            // Novo campo: emoji da categoria pai
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

    /**
     * Popula dados da subcategoria
     */
    private function popularSubcategoria($subcategoria, $dados)
    {
        $campos = ['categoria_id', 'nome', 'descricao', 'icone', 'imagem', 'ordem', 'ativo'];
        
        foreach ($campos as $campo) {
            if (isset($dados[$campo])) {
                $subcategoria->$campo = $dados[$campo];
            }
        }
    }
}