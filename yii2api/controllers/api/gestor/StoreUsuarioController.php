<?php
// controllers/api/gestor/StoreUsuarioController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\lojista\LojistaUsuario;
use app\models\api\lojista\LojistaUsuarioLoja;
use app\models\api\app\Loja;
use app\controllers\api\gestor\ControllerBase;
use yii\web\BadRequestHttpException;

class StoreUsuarioController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/store-usuarios
     * Lista lojistas com filtros (loja_id, funcao, status) e paginação
     */
    public function actionIndex()
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $request = Yii::$app->request;
            
            // Query base - exclui deletados (já filtrado no find() do model)
            $query = LojistaUsuario::find()
                ->orderBy(['criado_em' => SORT_DESC]);
            
            // Filtro por loja
            if ($request->get('loja_id')) {
                $lojaId = (int)$request->get('loja_id');
                $query->joinWith('lojasRelacionadas sul')
                      ->andWhere(['sul.loja_id' => $lojaId]);
            }
            
            // Filtro por função
            if ($request->get('funcao')) {
                $funcao = $request->get('funcao');
                $query->andWhere(['funcao' => $funcao]);
            }
            
            // Filtro por status
            if ($request->get('status') !== null) {
                $status = (int)$request->get('status');
                $query->andWhere(['status' => $status]);
            }
            
            // Filtro por busca (texto)
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'nome', $search],
                    ['like', 'email', $search],
                    ['like', 'cpf_cnpj', $search],
                ]);
            }
            
            // Paginação
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;
            
            $total = $query->count();
            $lojistas = $query->offset($offset)->limit($perPage)->all();
            
            // Formata dados com lojas associadas
            $data = array_map(function($lojista) {
                return $this->formatarLojista($lojista);
            }, $lojistas);
            
            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ]
            ], 'Lista de lojistas recuperada com sucesso');
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    /**
     * GET /api/gestor/store-usuarios/<id>
     * Visualiza um lojista específico com suas lojas associadas
     */
    public function actionView($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $lojista = LojistaUsuario::findOne(['id' => (int)$id]);
            
            if (!$lojista) {
                return ApiResponse::error('Lojista não encontrado', 404);
            }
            
            return ApiResponse::success(
                $this->formatarLojista($lojista),
                'Lojista recuperado com sucesso'
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    /**
     * POST /api/gestor/store-usuarios/create
     * Cria um novo lojista com associação a lojas
     * 
     * Campos esperados:
     * - nome (obrigatório)
     * - email (obrigatório)
     * - telefone (opcional)
     * - cpf_cnpj (opcional)
     * - funcao (obrigatório: proprietario, gerente, vendedor)
     * - status (opcional: 0 ou 1, padrão 1)
     * - senha (obrigatório para criação)
     * - loja_ids (array de IDs de lojas para associar)
     */
    public function actionCreate()
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $request = Yii::$app->request->post();
            
            // Validação básica
            $erros = [];
            if (empty($request['nome'])) $erros['nome'] = 'Nome é obrigatório';
            if (empty($request['email'])) $erros['email'] = 'E-mail é obrigatório';
            if (empty($request['funcao'])) $erros['funcao'] = 'Função é obrigatória';
            if (empty($request['senha'])) $erros['senha'] = 'Senha é obrigatória para nova criação';
            
            if (!empty($erros)) {
                return ApiResponse::error('Validação falhou', 422, null, $erros);
            }
            
            // Verifica se e-mail já existe
            if (LojistaUsuario::findOne(['email' => $request['email']])) {
                return ApiResponse::error('E-mail já está cadastrado', 422, null, ['email' => 'E-mail duplicado']);
            }
            
            // Cria modelo
            $lojista = new LojistaUsuario();
            $lojista->nome = $request['nome'];
            $lojista->email = $request['email'];
            $lojista->telefone = $request['telefone'] ?? null;
            $lojista->cpf_cnpj = $request['cpf_cnpj'] ?? null;
            $lojista->funcao = $request['funcao'];
            $lojista->status = $request['status'] ?? LojistaUsuario::STATUS_ATIVO;
            
            // Gera auth_key
            $lojista->generateAuthKey();
            
            // Define senha
            $lojista->setPassword($request['senha']);
            
            // Salva
            if (!$lojista->save()) {
                return ApiResponse::error('Erro ao criar lojista', 500, null, $lojista->errors);
            }
            
            // Associa lojas
            $lojaIds = $request['loja_ids'] ?? [];
            if (!empty($lojaIds)) {
                $lojista->assignLojas($lojaIds);
            }
            
            // Gera access token
            $accessToken = $lojista->generateAccessToken();
            
            return ApiResponse::success(
                $this->formatarLojista($lojista),
                'Lojista criado com sucesso'
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * PUT /api/gestor/store-usuarios/update/<id>
     * Atualiza um lojista existente e suas associações
     */
    public function actionUpdate($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $lojista = LojistaUsuario::findOne(['id' => (int)$id]);
            
            if (!$lojista) {
                return ApiResponse::error('Lojista não encontrado', 404);
            }
            
            $request = Yii::$app->request->post();
            
            // Atualiza campos
            if (!empty($request['nome'])) $lojista->nome = $request['nome'];
            if (!empty($request['telefone'])) $lojista->telefone = $request['telefone'];
            if (!empty($request['cpf_cnpj'])) $lojista->cpf_cnpj = $request['cpf_cnpj'];
            if (!empty($request['funcao'])) $lojista->funcao = $request['funcao'];
            if (isset($request['status'])) $lojista->status = (int)$request['status'];
            
            // Se enviou nova senha, atualiza
            if (!empty($request['senha'])) {
                $lojista->setPassword($request['senha']);
                $lojista->generateAuthKey();
            }
            
            // Salva
            if (!$lojista->save()) {
                return ApiResponse::error('Erro ao atualizar lojista', 500, null, $lojista->errors);
            }
            
            // Atualiza associações de lojas
            $lojaIds = $request['loja_ids'] ?? [];
            $lojista->assignLojas($lojaIds);
            
            return ApiResponse::success(
                $this->formatarLojista($lojista),
                'Lojista atualizado com sucesso'
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/gestor/store-usuarios/delete/<id>
     * Soft delete do lojista
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $lojista = LojistaUsuario::findOne(['id' => (int)$id]);
            
            if (!$lojista) {
                return ApiResponse::error('Lojista não encontrado', 404);
            }
            
            // Soft delete
            if ($lojista->softDelete()) {
                return ApiResponse::success(
                    null,
                    'Lojista removido com sucesso'
                );
            }
            
            return ApiResponse::error('Erro ao remover lojista', 500);
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/gestor/store-usuarios/options
     * Retorna lista de lojas para combobox (para associação)
     */
    public function actionOptions()
    {
        try {
            $lojas = Loja::find()
                ->select(['id', 'nome'])
                ->where(['deletado_em' => null])
                ->orderBy(['nome' => SORT_ASC])
                ->asArray()
                ->all();
            
            return ApiResponse::success($lojas, 'Lista de lojas');
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/gestor/store-usuarios/lojas-options
     * Retorna lista de lojas para seleção múltipla
     */
    public function actionLojasOptions()
    {
        try {
            $lojas = Loja::find()
                ->select(['id', 'nome'])
                ->where(['deletado_em' => null])
                ->orderBy(['nome' => SORT_ASC])
                ->asArray()
                ->all();
            
            return ApiResponse::success($lojas, 'Lista de lojas disponíveis');
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Formata os dados do lojista para retorno
     */
    private function formatarLojista(LojistaUsuario $lojista)
    {
        // Carrega as lojas associadas
        $lojasRelacionadas = LojistaUsuarioLoja::find()
            ->where(['usuario_id' => $lojista->id])
            ->with('loja')
            ->asArray()
            ->all();
        
        $lojas = array_map(function($rel) {
            return [
                'id' => $rel['loja']['id'],
                'nome' => $rel['loja']['nome'],
            ];
        }, $lojasRelacionadas);
        
        return [
            'id' => $lojista->id,
            'nome' => $lojista->nome,
            'email' => $lojista->email,
            'telefone' => $lojista->telefone,
            'cpf_cnpj' => $lojista->cpf_cnpj,
            'funcao' => $lojista->funcao,
            'status' => $lojista->status,
            'ultimo_login_em' => $lojista->ultimo_login_em,
            'ultimo_login_ip' => $lojista->ultimo_login_ip,
            'criado_em' => $lojista->criado_em,
            'atualizado_em' => $lojista->atualizado_em,
            'loja_ids' => array_column($lojas, 'id'),
            'lojas' => $lojas,
        ];
    }
}
