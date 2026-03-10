<?php
// controllers/api/gestor/LojaController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\Loja;
use app\controllers\api\gestor\ControllerBase;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class LojaController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/lojas
     * Lista todas as lojas com paginação e filtros
     */
    public function actionIndex()
    {
        try {
            $this->getUserByToken();// apenas autenticado

            $request = Yii::$app->request;

            $query = Loja::find()
                ->orderBy(['criado_em' => SORT_DESC]);

            // Filtros
            if ($request->get('categoria')) {
                $query->andWhere(['categoria' => $request->get('categoria')]);
            }

            if ($request->get('status')) {
                $query->andWhere(['status' => $request->get('status')]);
            }

            if ($request->get('verificado') !== null) {
                $query->andWhere(['verificado' => $request->get('verificado')]);
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
                    ['like', 'cidade', $search],
                ]);
            }

            // Paginação
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $lojas = $query->offset($offset)->limit($perPage)->all();

            $data = array_map(function($loja) {
                return $this->formatarLoja($loja);
            }, $lojas);

            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ]
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
            $this->getUserByToken(); // autenticado
            // Opcional: verificar se é admin
            // $this->verificarAdmin();

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

            // Verifica se slug já existe (se informado, mas será gerado automaticamente)
            // O sluggable behavior cuida disso, mas podemos verificar duplicidade de nome?
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
}