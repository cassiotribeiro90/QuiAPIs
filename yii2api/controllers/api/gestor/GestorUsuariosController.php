<?php
// controllers/api/gestor/GestorUsuariosController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\GestorUsuario;
use app\controllers\api\gestor\ControllerBase;

class GestorUsuariosController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/gestor-usuarios
     * Lista todos os gestores com paginação
     */
    public function actionIndex()
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            if ($usuarioLogado->nivel !== 'admin') {
                return $this->actionMe();
            }
            
            $request = Yii::$app->request;
            
            $query = GestorUsuario::find()
                ->where(['deletado_em' => null])
                ->orderBy(['criado_em' => SORT_DESC]);
            
            // Filtro por nível
            if ($request->get('nivel')) {
                $niveis = explode(',', $request->get('nivel'));
                $niveis = array_map('trim', $niveis);
                $niveis = array_filter($niveis);
                
                if (!empty($niveis)) {
                    $query->andWhere(['in', 'nivel', $niveis]);
                }
            }
            
            // Filtro por status
            if ($request->get('status') !== null) {
                $statusList = explode(',', $request->get('status'));
                $statusList = array_map('intval', $statusList);
                $statusList = array_filter($statusList, function($value) {
                    return in_array($value, [0, 1, 2]);
                });
                
                if (!empty($statusList)) {
                    $query->andWhere(['in', 'status', $statusList]);
                }
            }
            
            // Filtro por busca
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'nome', $search],
                    ['like', 'email', $search],
                    ['like', 'cpf', $search],
                ]);
            }
            
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;
            
            $total = $query->count();
            $gestores = $query->offset($offset)->limit($perPage)->all();
            
            $data = array_map(function($gestor) {
                return $this->formatarGestor($gestor, false);
            }, $gestores);
            
            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ]
            ], 'Lista de gestores recuperada com sucesso');
            
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'internal_error'
            );
        }
    }

    /**
     * GET /api/gestor/gestor-usuarios/<id>
     * Visualiza um gestor específico
     */
    public function actionView($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            $gestor = $this->findModel($id);
            
            if ($usuarioLogado->nivel !== 'admin' && $usuarioLogado->id != $id) {
                return ApiResponse::error(
                    'Você não tem permissão para visualizar este usuário',
                    403,
                    'forbidden'
                );
            }
            
            return ApiResponse::success(
                $this->formatarGestor($gestor, true),
                'Gestor encontrado com sucesso'
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
     * POST /api/gestor/gestor-usuarios/login
     * Login do gestor com device_id e device_token
     */
    public function actionLogin()
    {
        try {
            $request = Yii::$app->request;
            $email = $request->post('email');
            $senha = $request->post('senha');
            $deviceId = $request->post('device_id');
            $deviceToken = $request->post('device_token');
            
            if (empty($email) || empty($senha)) {
                return ApiResponse::error(
                    'Email e senha obrigatórios',
                    400,
                    'missing_fields'
                );
            }
            
            $gestor = GestorUsuario::findByEmail($email);
            
            if (!$gestor || !$gestor->validatePassword($senha)) {
                sleep(1);
                return ApiResponse::error(
                    'Email ou senha inválidos',
                    401,
                    'invalid_credentials'
                );
            }
            
            if (!$gestor->isAtivo()) {
                return ApiResponse::error(
                    'Usuário inativo',
                    401,
                    'inactive_user'
                );
            }

            // 🔥 ATUALIZA DEVICE_ID E DEVICE_TOKEN
            if (!empty($deviceId)) {
                $gestor->device_id = $deviceId;
            }
            if (!empty($deviceToken)) {
                $gestor->device_token = $deviceToken;
            }

            // 🔥 ATUALIZA METADADOS DO LOGIN
            $gestor->ultimo_login_em = date('Y-m-d H:i:s');
            $gestor->ultimo_login_ip = $request->userIP;

            // 🔥 SALVA (COM VERIFICAÇÃO DE ERRO)
            if (!$gestor->save()) {
                Yii::error('[LOGIN] Erro ao salvar gestor: ' . json_encode($gestor->errors), __METHOD__);
                return ApiResponse::error(
                    'Erro ao salvar dados do usuário',
                    500,
                    'save_error'
                );
            }

            // 🔥 GERA TOKENS
            $accessToken = $gestor->generateAccessToken();
            $refreshToken = $gestor->generateRefreshToken();
            
            return ApiResponse::success([
                'id' => $gestor->id,
                'nome' => $gestor->nome,
                'email' => $gestor->email,
                'nivel' => $gestor->nivel,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 7200,
                'token_type' => 'Bearer',
                'device_id' => $gestor->device_id,
                'device_token' => $gestor->device_token,
            ], 'Login realizado com sucesso');
            
        } catch (\Exception $e) {
            Yii::error('[LOGIN] Exceção: ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine(), __METHOD__);
            return ApiResponse::error(
                'Erro no login',
                500,
                'login_error'
            );
        }
    }

    /**
     * POST /api/gestor/gestor-usuarios/logout
     * Logout do gestor
     * $gestor @GestorUsuario
     */
    public function actionLogout()
    {
        try {
            /** @var GestorUsuario|null $gestor */
            $gestor = $this->getUserByToken();
            
            if ($gestor) {
                // 🔥 REMOVE DEVICE_TOKEN E DEVICE_ID AO LOGOUT
                $gestor->device_token = null;
                $gestor->device_id = null;
                $gestor->save(false);
                Yii::info("[GESTOR] Device ID e token removidos (logout)", __METHOD__);
                
                $gestor->invalidateTokens();
            }
            
            return ApiResponse::success(null, 'Logout realizado com sucesso');
            
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 401,
                'unauthorized'
            );
        }
    }

    /**
     * GET /api/gestor/gestor-usuarios/me
     * Dados do gestor logado
     */
    public function actionMe()
    {
        try {
            $gestor = $this->getUserByToken();
            
            return ApiResponse::success(
                $this->formatarGestor($gestor, true),
                'Dados do usuário recuperados com sucesso'
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 401,
                'unauthorized'
            );
        }
    }

    /**
     * POST /api/gestor/gestor-usuarios/create
     * Cria um novo gestor
     */
    public function actionCreate()
    {
        try {
            $this->getUserByToken();
    
            $request = Yii::$app->request;
            $dados = $request->post();
            
            $erros = [];
            if (empty($dados['nome'])) $erros['nome'][] = 'Nome é obrigatório';
            if (empty($dados['email'])) $erros['email'][] = 'Email é obrigatório';
            if (empty($dados['senha'])) $erros['senha'][] = 'Senha é obrigatória';
            
            if (!empty($erros)) {
                return ApiResponse::error(
                    'Campos obrigatórios não preenchidos',
                    400,
                    'missing_fields',
                    $erros
                );
            }
            
            if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                return ApiResponse::error(
                    'Email inválido',
                    400,
                    'invalid_email',
                    ['email' => ['Formato de email inválido']]
                );
            }
            
            if (strlen($dados['senha']) < 6) {
                return ApiResponse::error(
                    'Senha deve ter no mínimo 6 caracteres',
                    400,
                    'weak_password',
                    ['senha' => ['Senha muito curta']]
                );
            }
            
            if (GestorUsuario::find()->where(['email' => $dados['email']])->exists()) {
                return ApiResponse::error(
                    'Email já cadastrado',
                    409,
                    'duplicate_email',
                    ['email' => ['Este email já está em uso']]
                );
            }
            
            if (!empty($dados['cpf'])) {
                $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
                if (GestorUsuario::find()->where(['cpf' => $cpf])->exists()) {
                    return ApiResponse::error(
                        'CPF já cadastrado',
                        409,
                        'duplicate_cpf',
                        ['cpf' => ['Este CPF já está em uso']]
                    );
                }
                $dados['cpf'] = $cpf;
            }
            
            $gestor = new GestorUsuario();
            $this->popularGestor($gestor, $dados);
            $gestor->setPassword($dados['senha']);
            $gestor->generateAuthKey();
            $gestor->status = GestorUsuario::STATUS_ATIVO;
            
            if ($gestor->save()) {
                $token = $gestor->generateAccessToken();
                
                return ApiResponse::success(
                    $this->formatarGestor($gestor, true),
                    'Gestor cadastrado com sucesso',
                    201
                );
            }
            
            return ApiResponse::error(
                'Erro ao cadastrar gestor',
                422,
                'validation_failed',
                $gestor->errors
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
     * DELETE /api/gestor/gestor-usuarios/delete/<id>
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            if ($usuarioLogado->nivel !== 'admin') {
                return ApiResponse::error(
                    'Apenas administradores podem remover usuários',
                    403,
                    'forbidden'
                );
            }
            
            if ($usuarioLogado->id == $id) {
                return ApiResponse::error(
                    'Você não pode remover seu próprio usuário',
                    400,
                    'self_delete_not_allowed'
                );
            }
            
            $gestor = $this->findModel($id);
            
            $gestor->deletado_em = date('Y-m-d H:i:s');
            $gestor->status = GestorUsuario::STATUS_INATIVO;
            $gestor->device_token = null;
            $gestor->device_id = null;
            $gestor->invalidateTokens();
            
            if ($gestor->save(false)) {
                return ApiResponse::success(
                    null,
                    'Gestor removido com sucesso'
                );
            }
            
            return ApiResponse::error(
                'Erro ao remover gestor',
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
     * PUT /api/gestor/gestor-usuarios/update/<id>
     */
    public function actionUpdate($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            $gestor = $this->findModel($id);
            
            if ($usuarioLogado->nivel !== 'admin' && $usuarioLogado->id != $id) {
                return ApiResponse::error(
                    'Você não tem permissão para atualizar este usuário',
                    403,
                    'forbidden'
                );
            }
            
            $request = Yii::$app->request;
            $dados = $request->post();
            
            if (!empty($dados['email']) && $dados['email'] !== $gestor->email) {
                if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                    return ApiResponse::error(
                        'Email inválido',
                        400,
                        'invalid_email',
                        ['email' => ['Formato de email inválido']]
                    );
                }
                
                if (GestorUsuario::find()->where(['email' => $dados['email']])->andWhere(['!=', 'id', $id])->exists()) {
                    return ApiResponse::error(
                        'Email já cadastrado',
                        409,
                        'duplicate_email',
                        ['email' => ['Este email já está em uso']]
                    );
                }
            }
            
            if (!empty($dados['cpf'])) {
                $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
                if (GestorUsuario::find()->where(['cpf' => $cpf])->andWhere(['!=', 'id', $id])->exists()) {
                    return ApiResponse::error(
                        'CPF já cadastrado',
                        409,
                        'duplicate_cpf',
                        ['cpf' => ['Este CPF já está em uso']]
                    );
                }
                $dados['cpf'] = $cpf;
            }
            
            $this->popularGestor($gestor, $dados);
            
            if (!empty($dados['senha'])) {
                if (strlen($dados['senha']) < 6) {
                    return ApiResponse::error(
                        'Senha deve ter no mínimo 6 caracteres',
                        400,
                        'weak_password',
                        ['senha' => ['Senha muito curta']]
                    );
                }
                $gestor->setPassword($dados['senha']);
            }
            
            if ($gestor->save()) {
                return ApiResponse::success(
                    $this->formatarGestor($gestor, true),
                    'Gestor atualizado com sucesso'
                );
            }
            
            return ApiResponse::error(
                'Erro ao atualizar gestor',
                422,
                'validation_failed',
                $gestor->errors
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
     * POST /api/gestor/gestor-usuarios/refresh-token
     */
    public function actionRefreshToken()
    {
        try {
            $request = Yii::$app->request;
            $refreshToken = $request->post('refresh_token');
            
            if (empty($refreshToken)) {
                return ApiResponse::error(
                    'Refresh token é obrigatório',
                    400,
                    'missing_refresh_token'
                );
            }
            
            $gestor = GestorUsuario::findByRefreshToken($refreshToken);
            
            if (!$gestor) {
                return ApiResponse::error(
                    'Refresh token inválido ou expirado',
                    401,
                    'invalid_refresh_token'
                );
            }
            
            if (!$gestor->isAtivo()) {
                return ApiResponse::error(
                    'Usuário inativo',
                    401,
                    'inactive_user'
                );
            }
            
            $novoAccessToken = $gestor->generateAccessToken();
            
            $renovarRefresh = $request->post('renovar_refresh', false);
            $novoRefreshToken = $renovarRefresh ? $gestor->generateRefreshToken() : $refreshToken;
            
            return ApiResponse::success([
                'access_token' => $novoAccessToken,
                'refresh_token' => $novoRefreshToken,
                'expires_in' => 86400,
                'token_type' => 'Bearer'
            ], 'Token renovado com sucesso');
            
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 500,
                'refresh_error'
            );
        }
    }

    /**
     * GET /api/gestor/gestor-usuarios/check-token
     */
    public function actionCheckToken()
    {
        try {
            $this->getUserByToken();
            return ApiResponse::success(
                ['valid' => true],
                'Token válido'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Token inválido',
                401,
                'invalid_token',
                ['valid' => false]
            );
        }
    }

    /**
     * GET /api/gestor/gestor-usuarios/options
     */
    public function actionOptions()
    {
        try {
            $this->getUserByToken();
            
            return ApiResponse::success([
                'niveis' => [
                    ['value' => 'comercial', 'label' => 'Comercial'],
                    ['value' => 'admin', 'label' => 'Administrador'],
                    ['value' => 'suporte', 'label' => 'Suporte'],
                    ['value' => 'financeiro', 'label' => 'Financeiro'],
                ],
                'status' => [
                    ['value' => 1, 'label' => 'Ativo'],
                    ['value' => 0, 'label' => 'Inativo'],
                    ['value' => 2, 'label' => 'Bloqueado'],
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
     * POST /api/gestor/gestor-usuarios/device-token
     * Salva o device_id e device_token do gestor
     */
    public function actionDeviceToken()
    {
        try {
            $request = Yii::$app->request;
            $deviceToken = $request->post('device_token');
            $deviceId = $request->post('device_id');

            if (empty($deviceToken) && empty($deviceId)) {
                return ApiResponse::error('device_token ou device_id é obrigatório', 400);
            }
            /** @var GestorUsuario|null $gestor */
            $gestor = $this->getUserByToken();
            if (!$gestor) {
                return ApiResponse::error('Gestor não autenticado', 401);
            }

            if (!empty($deviceId)) {
                $gestor->device_id = $deviceId;
            }

            if (!empty($deviceToken)) {
                $gestor->device_token = $deviceToken;
            }

            $gestor->save(false);

            return ApiResponse::success([
                'message' => 'Dados do dispositivo salvos com sucesso',
                'device_id' => $gestor->device_id,
                'device_token' => $gestor->device_token,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/gestor/gestor-usuarios/device-token
     * Remove o device token (logout)
     */
    public function actionDeleteDeviceToken()
    {
        try {
            /** @var GestorUsuario|null $gestor */
            $gestor = $this->getUserByToken();
            if (!$gestor) {
                return ApiResponse::error('Gestor não autenticado', 401);
            }

            $gestor->device_token = null;
            $gestor->device_id = null;
            $gestor->save(false);

            return ApiResponse::success([
                'message' => 'Dados do dispositivo removidos com sucesso',
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /** @var GestorUsuario|null $gestor */
    private function findModel($id)
    {
        $gestor = GestorUsuario::find()
            ->where(['id' => $id])
            ->andWhere(['deletado_em' => null])
            ->one();
            
        if (!$gestor) {
            throw new \yii\web\NotFoundHttpException('Gestor não encontrado');
        }
        
        return $gestor;
    }

    private function formatarGestor($gestor, $detalhado = false)
    {
        $dados = [
            'id' => $gestor->id,
            'nome' => $gestor->nome,
            'email' => $gestor->email,
            'nivel' => $gestor->nivel,
            'status' => (int)$gestor->status,
            'status_label' => $this->getStatusLabel($gestor->status),
            'criado_em' => $gestor->criado_em,
            'device_id' => $gestor->device_id,
            'device_token' => $gestor->device_token,
        ];
        
        if ($detalhado) {
            $dados = array_merge($dados, [
                'cpf' => $gestor->cpf,
                'telefone' => $gestor->telefone,
                'ultimo_login_em' => $gestor->ultimo_login_em,
                'ultimo_login_ip' => $gestor->ultimo_login_ip,
                'atualizado_em' => $gestor->atualizado_em,
            ]);
        }
        
        return $dados;
    }

    private function getStatusLabel($status)
    {
        $labels = [
            GestorUsuario::STATUS_ATIVO => 'Ativo',
            GestorUsuario::STATUS_INATIVO => 'Inativo',
            GestorUsuario::STATUS_BLOQUEADO => 'Bloqueado',
        ];
        
        return $labels[$status] ?? 'Desconhecido';
    }

    private function popularGestor($gestor, $dados)
    {
        $camposPermitidos = ['nome', 'email', 'cpf', 'telefone', 'nivel', 'status'];
        
        foreach ($camposPermitidos as $campo) {
            if (isset($dados[$campo])) {
                $gestor->$campo = $dados[$campo];
            }
        }
    }
}