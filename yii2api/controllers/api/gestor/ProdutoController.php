<?php
namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\Produto;
use app\models\api\gestor\Loja;
use app\models\api\gestor\Categoria;
use app\models\api\gestor\Subcategoria;
use app\controllers\api\gestor\ControllerBase;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class ProdutoController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/produtos
     * Lista todos os produtos (com paginação e filtros)
     */
    public function actionIndex()
    {
        try {
            $this->getUserByToken();

            $request = Yii::$app->request;

            $query = Produto::find()
                ->where(['deletado_em' => null])
                ->orderBy(['criado_em' => SORT_DESC]);


            // ===== FILTROS =====
            if ($request->get('loja_id')) {
                $query->andWhere(['loja_id' => $request->get('loja_id')]);
            }

            // Filtro por categoria (múltiplos valores separados por vírgula)
            if ($request->get('categoria')) {
                $categoriaList = explode(',', $request->get('categoria'));
                $categoriaList = array_map('trim', $categoriaList);
                $categoriaList = array_filter($categoriaList);
                if (!empty($categoriaList)) {
                    $query->andWhere(['in', 'categoria', $categoriaList]);
                }
            }

            if ($request->get('disponivel') !== null) {
                $query->andWhere(['disponivel' => (int)$request->get('disponivel')]);
            }

            if ($request->get('ativo') !== null) {
                $query->andWhere(['ativo' => (int)$request->get('ativo')]);
            }

            // Busca geral (nome, descrição, categoria)
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'nome', $search],
                    ['like', 'descricao', $search],
                    ['like', 'categoria', $search],
                ]);
            }

            // ===== PAGINAÇÃO =====
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $produtos = $query->offset($offset)->limit($perPage)->all();

            // ===== FORMATAR DADOS =====
            $data = array_map(function($produto) {
                return $this->formatarProduto($produto);
            }, $produtos);

            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ]
            ], 'Produtos recuperados com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'internal_error'
            );
        }
    }

       /**
     * GET /api/gestor/produtos/<id>
     * Visualiza um produto específico
     */
    public function actionView($id)
    {
        try {
            $this->getUserByToken();
            
            // Carrega o produto com relacionamentos
            $produto = Produto::find()
                ->where(['id' => $id])
                ->andWhere(['deletado_em' => null])
                ->with(['subcategoria.categoria'])
                ->one();

            if (!$produto) {
                throw new NotFoundHttpException('Produto não encontrado');
            }

            // Buscar todas as categorias ativas
            $categorias = Categoria::find()
                ->where(['ativo' => 1])
                ->orderBy(['ordem' => SORT_ASC])
                ->all();

            // Buscar subcategorias da categoria do produto
            $categoriaDoProduto = $produto->subcategoria ? $produto->subcategoria->categoria : null;
            $subcategorias = [];
            
            if ($categoriaDoProduto) {
                $subcategorias = Subcategoria::find()
                    ->where(['ativo' => 1, 'categoria_id' => $categoriaDoProduto->id])
                    ->orderBy(['nome' => SORT_ASC])
                    ->all();
            }

            // Formatar dados
            $categoriasData = array_map(function($cat) {
                return [
                    'id' => $cat->id,
                    'nome' => $cat->nome,
                    'icone' => $cat->icone,
                    'cor' => $cat->cor,
                ];
            }, $categorias);

            $subcategoriasData = array_map(function($sub) {
                return [
                    'id' => $sub->id,
                    'categoria_id' => $sub->categoria_id,
                    'nome' => $sub->nome,
                ];
            }, $subcategorias);

            return ApiResponse::success([
                'produto' => $this->formatarProduto($produto, true),
                'options' => [
                    'categorias' => $categoriasData,
                    'subcategorias' => $subcategoriasData,
                ]
            ], 'Produto encontrado com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 404,
                'not_found'
            );
        }
    }

    /**
     * POST /api/gestor/produtos
     * Cria um novo produto
     */
    public function actionCreate()
    {
        try {
            $this->getUserByToken();

            $dados = Yii::$app->request->post();

            // Valida campos obrigatórios
            $obrigatorios = ['loja_id', 'nome', 'preco'];
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

            // Verifica se a loja existe
            $loja = Loja::findOne($dados['loja_id']);
            if (!$loja) {
                return ApiResponse::error(
                    'Loja não encontrada',
                    400,
                    'invalid_loja'
                );
            }

            $produto = new Produto();
            $this->popularProduto($produto, $dados);

            if ($produto->save()) {
                return ApiResponse::success(
                    $this->formatarProduto($produto, true),
                    'Produto criado com sucesso',
                    201
                );
            }

            return ApiResponse::error(
                'Erro ao criar produto',
                422,
                'validation_failed',
                $produto->errors
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
     * PUT /api/gestor/produtos/<id>
     * Atualiza um produto
     */
    public function actionUpdate($id)
    {
        try {
            $this->getUserByToken();
            $produto = $this->findModel($id);

            $dados = Yii::$app->request->post();

            $this->popularProduto($produto, $dados);

            if ($produto->save()) {
                return ApiResponse::success(
                    $this->formatarProduto($produto, true),
                    'Produto atualizado com sucesso'
                );
            }

            return ApiResponse::error(
                'Erro ao atualizar produto',
                422,
                'validation_failed',
                $produto->errors
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
     * DELETE /api/gestor/produtos/<id>
     * Remove (soft delete) um produto
     */
    public function actionDelete($id)
    {
        try {
            $this->getUserByToken();
            $produto = $this->findModel($id);


            /** @var Produto $produto */
            if ($produto->softDelete()) {
                return ApiResponse::success(null, 'Produto removido com sucesso');
            }

            return ApiResponse::error(
                'Erro ao remover produto',
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
     * Busca model pelo ID
     */
    private function findModel($id)
    {
        $produto = Produto::find()
            ->where(['id' => $id])
            ->andWhere(['deletado_em' => null])
            ->one();

        if (!$produto) {
            throw new NotFoundHttpException('Produto não encontrado');
        }

        return $produto;
    }

    private function formatarProduto($produto, $detalhado = false)
{
    // Obtém categoria através da subcategoria (relacionamento)
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
        
        // ✅ CAMPOS CORRETOS DE CATEGORIA
        'categoria_id' => $categoria ? $categoria->id : null,
        'categoria_nome' => $categoria ? $categoria->nome : null,
        'categoria_icone' => $categoria ? $categoria->icone : null,
        
        // Mantido para compatibilidade (mas pode remover depois)
        'categoria' => $categoria ? $categoria->nome : null,
        
        'subcategoria_id' => $produto->subcategoria_id,
        'subcategoria_nome' => $produto->subcategoria ? $produto->subcategoria->nome : null,
        
        'disponivel' => (bool)$produto->disponivel,
        'ativo' => (bool)$produto->ativo,
        'destaque' => (bool)$produto->destaque,
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
            'variacoes' => $produto->variacoes,
            'opcoes' => $produto->opcoes,
            'estoque' => $produto->estoque,
        ]);
    }

    return $dados;
}

    /**
     * Popula dados do produto
     */
    private function popularProduto($produto, $dados)
    {
        $campos = [
            'loja_id', 'subcategoria_id', 'nome', 'descricao', 'preco',
            'preco_promocional', 'imagem', 'imagens', 'ingredientes',
            'ingredientes_texto', 'calorias', 'peso_gramas',
            'contem_gluten', 'contem_lactose', 'vegano', 'vegetariano',
            'apimentado', 'selos', 'disponivel_inicio', 'disponivel_fim',
            'disponivel_dias', 'variacoes', 'opcoes', 'tempo_preparo_min',
            'disponivel', 'estoque', 'ordem', 'ativo', 'destaque'
        ];

        foreach ($campos as $campo) {
            if (isset($dados[$campo])) {
                $produto->$campo = $dados[$campo];
            }
        }
    }
}