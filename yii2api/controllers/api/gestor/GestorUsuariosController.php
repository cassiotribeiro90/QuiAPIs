<?php
// controllers/api/gestor/GestorUsuariosController.php

namespace app\controllers\api\gestor;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ConflictHttpException;
use app\models\api\gestor\GestorUsuario;
use app\controllers\api\gestor\ControllerBase;

class GestorUsuariosController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * POST /api/gestor/gestor-usuarios/login
     * Login do gestor - retorna access_token e refresh_token
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $email = $request->post('email');
        $senha = $request->post('senha');
        
        if (empty($email) || empty($senha)) {
            throw new BadRequestHttpException('Email e senha obrigatórios');
        }
        
        $gestor = GestorUsuario::findByEmail($email);
        
        if (!$gestor || !$gestor->validatePassword($senha)) {
            throw new UnauthorizedHttpException('Email ou senha inválidos');
        }
        
        if (!$gestor->isAtivo()) {
            throw new UnauthorizedHttpException('Usuário inativo');
        }
        
        $gestor->ultimo_login_em = date('Y-m-d H:i:s');
        $gestor->ultimo_login_ip = $request->userIP;
        
        $accessToken = $gestor->generateAccessToken();
        $refreshToken = $gestor->generateRefreshToken();
        
        return [
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'id' => $gestor->id,
                'nome' => $gestor->nome,
                'email' => $gestor->email,
                'nivel' => $gestor->nivel,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 7200,
                'token_type' => 'Bearer'
            ]
        ];
    }

    /**
     * POST /api/gestor/gestor-usuarios/create
     * Cadastra um novo gestor (apenas admin pode criar)
     */
    public function actionCreate()
    {
        // Verifica se é admin (opcional - descomente se quiser restringir)
        // $this->verificarAdmin();
        
        $request = Yii::$app->request;
        $dados = $request->post();
        
        // Valida campos obrigatórios
        $camposObrigatorios = ['nome', 'email', 'senha'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                throw new BadRequestHttpException("Campo '$campo' é obrigatório");
            }
        }
        
        // Valida email
        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException('Email inválido');
        }
        
        // Valida senha (mínimo 6 caracteres)
        if (strlen($dados['senha']) < 6) {
            throw new BadRequestHttpException('Senha deve ter no mínimo 6 caracteres');
        }
        
        // Verifica se email já existe
        if (GestorUsuario::find()->where(['email' => $dados['email']])->exists()) {
            throw new ConflictHttpException('Email já cadastrado');
        }
        
        // Verifica se CPF já existe (se fornecido)
        if (!empty($dados['cpf'])) {
            $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
            if (GestorUsuario::find()->where(['cpf' => $cpf])->exists()) {
                throw new ConflictHttpException('CPF já cadastrado');
            }
            $dados['cpf'] = $cpf;
        }
        
        // Cria novo gestor
        $gestor = new GestorUsuario();
        $gestor->nome = $dados['nome'];
        $gestor->email = $dados['email'];
        $gestor->setPassword($dados['senha']);
        $gestor->generateAuthKey();
        
        // Nível (padrão: 'comercial' se não informado)
        $gestor->nivel = $dados['nivel'] ?? 'comercial';
        
        // Status (1 = ativo)
        $gestor->status = GestorUsuario::STATUS_ATIVO;
        
        // Campos opcionais
        if (!empty($dados['cpf'])) {
            $gestor->cpf = $dados['cpf'];
        }
        if (!empty($dados['telefone'])) {
            $gestor->telefone = $dados['telefone'];
        }
        
        if ($gestor->save()) {
            // Gera token se quiser retornar já logado
            $token = $gestor->generateAccessToken();
            
            return [
                'success' => true,
                'message' => 'Gestor cadastrado com sucesso',
                'data' => [
                    'id' => $gestor->id,
                    'nome' => $gestor->nome,
                    'email' => $gestor->email,
                    'nivel' => $gestor->nivel,
                    'access_token' => $token,
                    'created_at' => $gestor->criado_em,
                ]
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return [
                'success' => false,
                'message' => 'Erro ao cadastrar gestor',
                'errors' => $gestor->errors
            ];
        }
    }

    /**
     * POST /api/gestor/gestor-usuarios/refresh
     * Renova access_token usando refresh_token
     */
    public function actionRefresh()
    {
        $request = Yii::$app->request;
        $refreshToken = $request->post('refresh_token');
        
        if (empty($refreshToken)) {
            throw new BadRequestHttpException('Refresh token é obrigatório');
        }
        
        $gestor = GestorUsuario::findByRefreshToken($refreshToken);
        
        if (!$gestor) {
            throw new UnauthorizedHttpException('Refresh token inválido ou expirado');
        }
        
        if (!$gestor->isAtivo()) {
            throw new UnauthorizedHttpException('Usuário inativo');
        }
        
        $novoAccessToken = $gestor->generateAccessToken(7200);
        
        $renovarRefreshToken = $request->post('renovar_refresh', false);
        $novoRefreshToken = null;
        
        if ($renovarRefreshToken) {
            $novoRefreshToken = $gestor->generateRefreshToken(2592000);
        }
        
        return [
            'success' => true,
            'message' => 'Token renovado com sucesso',
            'data' => [
                'access_token' => $novoAccessToken,
                'refresh_token' => $novoRefreshToken ?? $refreshToken,
                'expires_in' => 7200,
                'token_type' => 'Bearer'
            ]
        ];
    }

    /**
     * POST /api/gestor/gestor-usuarios/logout
     * Logout - invalida todos os tokens
     */
    public function actionLogout()
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        
        if ($token) {
            $gestor = GestorUsuario::findIdentityByAccessToken($token);
            if ($gestor) {
                $gestor->invalidateTokens();
            }
        }
        
        return [
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ];
    }

    /**
     * GET /api/gestor/gestor-usuarios/me
     * Dados do usuário logado
     */
    public function actionMe()
    {
        $gestor = $this->getUserFromToken();
        
        return [
            'success' => true,
            'data' => [
                'id' => $gestor->id,
                'nome' => $gestor->nome,
                'email' => $gestor->email,
                'nivel' => $gestor->nivel,
                'ultimo_login_em' => $gestor->ultimo_login_em,
                'criado_em' => $gestor->criado_em,
            ]
        ];
    }

    /**
     * Extrai usuário do token no header
     */
    private function getUserFromToken()
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');
        
        if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            throw new UnauthorizedHttpException('Token não fornecido');
        }
        
        $token = $matches[1];
        $gestor = GestorUsuario::findIdentityByAccessToken($token);
        
        if (!$gestor) {
            throw new UnauthorizedHttpException('Token inválido ou expirado');
        }
        
        return $gestor;
    }
    
    /**
     * Verifica se o usuário logado é admin
     */
    private function verificarAdmin()
    {
        $gestor = $this->getUserFromToken();
        
        if ($gestor->nivel !== 'admin') {
            throw new UnauthorizedHttpException('Acesso negado. Apenas administradores podem criar novos usuários.');
        }
        
        return true;
    }
}