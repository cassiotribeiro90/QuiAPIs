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
            
            // Se não for admin, retorna apenas seus próprios dados
            if ($usuarioLogado->nivel !== 'admin') {
                return $this->actionMe();
            }
            
            $request = Yii::$app->request;
            
            // Query base - exclui deletados
            $query = GestorUsuario::find()
                ->where(['deletado_em' => null])
                ->orderBy(['criado_em' => SORT_DESC]);
            
            // Filtros
            if ($request->get('nivel')) {
                $query->andWhere(['nivel' => $request->get('nivel')]);
            }
            
            if ($request->get('status') !== null) {
                $query->andWhere(['status' => $request->get('status')]);
            }
            
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'nome', $search],
                    ['like', 'email', $search],
                    ['like', 'cpf', $search],
                ]);
            }
            
            // Paginação
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;
            
            $total = $query->count();
            $gestores = $query->offset($offset)->limit($perPage)->all();
            
            // Formata dados
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
                $e instanceof \yii\web\UnauthorizedHttpException ? 'unauthorized' : 'internal_error'
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
            
            // Verifica permissão
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
     * Login do gestor
     */
    public function actionLogin()
    {
        try {
            $request = Yii::$app->request;
            $email = $request->post('email');
            $senha = $request->post('senha');
            
            if (empty($email) || empty($senha)) {
                return ApiResponse::error(
                    'Email e senha obrigatórios',
                    400,
                    'missing_fields'
                );
            }
            
            $gestor = GestorUsuario::findByEmail($email);
            
            if (!$gestor || !$gestor->validatePassword($senha)) {
                // Delay para evitar timing attack
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
            
            // Atualiza último login
            $gestor->ultimo_login_em = date('Y-m-d H:i:s');
            $gestor->ultimo_login_ip = $request->userIP;
            $gestor->save(false);
            
            // Gera tokens
            /** @var GestorUsuario $gestor */
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
                'token_type' => 'Bearer'
            ], 'Login realizado com sucesso');
            
        } catch (\Exception $e) {
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
     */
    public function actionLogout()
    {
        try {
            /** @var GestorUsuario $gestor */
            $gestor = $this->getUserByToken();
            
            if ($gestor) {
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
            // Verifica se é admin (descomente se necessário)
            // $this->verificarAdmin();
            $this->getUserByToken();
    
            $request = Yii::$app->request;
            $dados = $request->post();
            
            // Valida campos obrigatórios
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
            
            // Valida email
            if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                return ApiResponse::error(
                    'Email inválido',
                    400,
                    'invalid_email',
                    ['email' => ['Formato de email inválido']]
                );
            }
            
            // Valida senha
            if (strlen($dados['senha']) < 6) {
                return ApiResponse::error(
                    'Senha deve ter no mínimo 6 caracteres',
                    400,
                    'weak_password',
                    ['senha' => ['Senha muito curta']]
                );
            }
            
            // Verifica duplicidade de email
            if (GestorUsuario::find()->where(['email' => $dados['email']])->exists()) {
                return ApiResponse::error(
                    'Email já cadastrado',
                    409,
                    'duplicate_email',
                    ['email' => ['Este email já está em uso']]
                );
            }
            
            // Verifica duplicidade de CPF
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
            
            // Cria gestor
            $gestor = new GestorUsuario();
            $this->popularGestor($gestor, $dados);
            $gestor->setPassword($dados['senha']);
            $gestor->generateAuthKey();
            $gestor->status = GestorUsuario::STATUS_ATIVO;
            
            if ($gestor->save()) {
                $token = $gestor->generateAccessToken();
                
                return ApiResponse::success(
                    array_merge(
                        $this->formatarGestor($gestor, true),
                        // ['access_token' => $token]
                    ),
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
     * Remove (soft delete) um gestor
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            // Apenas admin pode deletar
            if ($usuarioLogado->nivel !== 'admin') {
                return ApiResponse::error(
                    'Apenas administradores podem remover usuários',
                    403,
                    'forbidden'
                );
            }
            
            // Não permite deletar a si mesmo
            if ($usuarioLogado->id == $id) {
                return ApiResponse::error(
                    'Você não pode remover seu próprio usuário',
                    400,
                    'self_delete_not_allowed'
                );
            }
            
            $gestor = $this->findModel($id);
            
            // Soft delete
            $gestor->deletado_em = date('Y-m-d H:i:s');
            $gestor->status = GestorUsuario::STATUS_INATIVO;
            $gestor->invalidateTokens(); // Invalida tokens do usuário deletado
            
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
     * Atualiza um gestor
     */
    public function actionUpdate($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            $gestor = $this->findModel($id);
            
            // Verifica permissão
            if ($usuarioLogado->nivel !== 'admin' && $usuarioLogado->id != $id) {
                return ApiResponse::error(
                    'Você não tem permissão para atualizar este usuário',
                    403,
                    'forbidden'
                );
            }
            
            $request = Yii::$app->request;
            $dados = $request->post();
            
            // Valida email se foi alterado
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
            
            // Valida CPF se foi alterado
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
            
            // Atualiza dados
            $this->popularGestor($gestor, $dados);
            
            // Atualiza senha se fornecida
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
     * Renova o access token usando refresh token
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
            
            // Gera novo access token
            $novoAccessToken = $gestor->generateAccessToken();
            
            // Opcional: renovar refresh token (rotação)
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
     * Verifica se o token é válido
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
     * Retorna opções para selects (níveis, status)
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
     * Busca model pelo ID
     */
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

    /**
     * Formata dados do gestor para resposta
     */
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

    /**
     * Retorna label do status
     */
    private function getStatusLabel($status)
    {
        $labels = [
            GestorUsuario::STATUS_ATIVO => 'Ativo',
            GestorUsuario::STATUS_INATIVO => 'Inativo',
            GestorUsuario::STATUS_BLOQUEADO => 'Bloqueado',
        ];
        
        return $labels[$status] ?? 'Desconhecido';
    }

    /**
     * Popula dados do gestor
     */
    private function popularGestor($gestor, $dados)
    {
        $camposPermitidos = ['nome', 'email', 'cpf', 'telefone', 'nivel', 'status'];
        
        foreach ($camposPermitidos as $campo) {
            if (isset($dados[$campo])) {
                $gestor->$campo = $dados[$campo];
            }
        }
    }

    /**
     * Verifica se usuário é admin
     */
    private function verificarAdmin()
    {
        $gestor = $this->getUserByToken();
        
        if ($gestor->nivel !== 'admin') {
            throw new \yii\web\ForbiddenHttpException('Acesso negado. Apenas administradores podem executar esta ação.');
        }
        
        return true;
    }
}