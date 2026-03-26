<?php
// controllers/api/app/AuthController.php

namespace app\controllers\api\app;

use Yii;
use app\controllers\api\app\ControllerBase;
use app\models\api\app\Usuario;

class AuthController extends AppControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * POST /api/app/auth/login
     * Login do cliente
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $email = $request->post('email');
        $senha = $request->post('senha');
        
        if (empty($email) || empty($senha)) {
            return $this->errorResponse('Email e senha são obrigatórios', 400);
        }
        
        $usuario = Usuario::findByEmail($email);
        
        if (!$usuario || !$usuario->validatePassword($senha)) {
            sleep(1); // delay para evitar timing attack
            return $this->errorResponse('Email ou senha inválidos', 401);
        }
        
        if (!$usuario->isAtivo()) {
            return $this->errorResponse('Usuário inativo', 401);
        }
        
        // Atualiza último login
        $usuario->ultimo_login_em = date('Y-m-d H:i:s');
        $usuario->ultimo_login_ip = $request->userIP;
        $usuario->login_count = ($usuario->login_count ?? 0) + 1;
        $usuario->save(false);
        
        // Gera tokens
        $accessToken = $usuario->generateAccessToken();
        $refreshToken = $usuario->generateRefreshToken();
        
        return $this->successResponse([
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 7200,
            'token_type' => 'Bearer',
        ], 'Login realizado com sucesso');
    }
    
    /**
     * POST /api/app/auth/cadastro
     * Cadastro de novo cliente
     */
    public function actionCadastro()
    {
        $dados = Yii::$app->request->post();
        
        // Valida campos obrigatórios
        if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
            return $this->errorResponse('Nome, email e senha são obrigatórios', 400);
        }
        
        // Valida email
        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Email inválido', 400);
        }
        
        // Valida senha
        if (strlen($dados['senha']) < 6) {
            return $this->errorResponse('Senha deve ter no mínimo 6 caracteres', 400);
        }
        
        // Verifica se email já existe
        if (Usuario::find()->where(['email' => $dados['email']])->exists()) {
            return $this->errorResponse('Email já cadastrado', 409);
        }
        
        // Cria usuário
        $usuario = new Usuario();
        $usuario->nome = $dados['nome'];
        $usuario->email = $dados['email'];
        $usuario->setPassword($dados['senha']);
        $usuario->generateAuthKey();
        $usuario->status = Usuario::STATUS_ATIVO;
        
        // Campos opcionais
        if (!empty($dados['cpf'])) {
            $usuario->cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
        }
        
        if (!empty($dados['telefone'])) {
            $usuario->telefone = $dados['telefone'];
        }
        
        if (!empty($dados['whatsapp'])) {
            $usuario->whatsapp = $dados['whatsapp'];
        }
        
        if ($usuario->save()) {
            $token = $usuario->generateAccessToken();
            $refreshToken = $usuario->generateRefreshToken();
            
            return $this->successResponse([
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'access_token' => $token,
                'refresh_token' => $refreshToken,
            ], 'Cadastro realizado com sucesso', 201);
        }
        
        return $this->errorResponse('Erro ao cadastrar', 422, null, $usuario->errors);
    }
    
    /**
     * POST /api/app/auth/refresh-token
     * Renova o access token
     */
    public function actionRefreshToken()
    {
        $refreshToken = Yii::$app->request->post('refresh_token');
        
        if (empty($refreshToken)) {
            return $this->errorResponse('Refresh token é obrigatório', 400);
        }
        
        $usuario = Usuario::findByRefreshToken($refreshToken);
        
        if (!$usuario) {
            return $this->errorResponse('Refresh token inválido ou expirado', 401);
        }
        
        if (!$usuario->isAtivo()) {
            return $this->errorResponse('Usuário inativo', 401);
        }
        
        $novoAccessToken = $usuario->generateAccessToken();
        
        return $this->successResponse([
            'access_token' => $novoAccessToken,
            'expires_in' => 7200,
            'token_type' => 'Bearer',
        ], 'Token renovado com sucesso');
    }
    
    /**
     * POST /api/app/auth/logout
     * Logout do cliente
     */
    public function actionLogout()
    {
        /** @var \app\models\api\app\Usuario $usuario */
        $usuario = $this->getUserByToken();
        $usuario->invalidateTokens();
        
        return $this->successResponse(null, 'Logout realizado com sucesso');
    }
    
    /**
     * GET /api/app/auth/me
     * Dados do usuário logado
     */
    public function actionMe()
    {
        $usuario = $this->getUserByToken();
        
        return $this->successResponse([
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'telefone' => $usuario->telefone,
            'whatsapp' => $usuario->whatsapp,
            'cpf' => $usuario->cpf,
            'ultimo_login_em' => $usuario->ultimo_login_em,
            'criado_em' => $usuario->criado_em,
        ]);
    }
}