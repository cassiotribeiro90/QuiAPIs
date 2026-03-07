<?php
// Arquivo: controllers/api/gestor/GestorUsuariosController.php

namespace app\controllers\api\gestor;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ConflictHttpException;
use app\models\api\gestor\GestorUsuario;
use app\controllers\api\gestor\ControllerBase;;

class GestorUsuariosController extends ControllerBase
{
    public $enableCsrfValidation = false;
    
    public function beforeAction($action)
    {
        // Define o formato JSON para todas as respostas
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }
    
    /**
     * POST /api/gestor/gestor-usuarios/login
     * Login do gestor
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        
        // Pega dados do POST (JSON ou form-data)
        $email = $request->post('email');
        $senha = $request->post('senha');
        
        // Validações básicas
        if (empty($email)) {
            throw new BadRequestHttpException('Email é obrigatório');
        }
        if (empty($senha)) {
            throw new BadRequestHttpException('Senha é obrigatória');
        }
        
        // Busca gestor pelo email
        $gestor = GestorUsuario::findByEmail($email);
        
        // Verifica se existe e se a senha está correta
        if (!$gestor || !$gestor->validatePassword($senha)) {
            throw new UnauthorizedHttpException('Email ou senha inválidos');
        }
        
        // Verifica se está ativo
        if (!$gestor->isAtivo()) {
            throw new UnauthorizedHttpException('Usuário inativo');
        }
        
        // Atualiza último login
        $gestor->ultimo_login_at = date('Y-m-d H:i:s');
        $gestor->ultimo_login_ip = $request->userIP;
        $gestor->save(false);
        
        // Gera novo token
        $token = $gestor->generateAccessToken();
        
        return [
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'id' => $gestor->id,
                'nome' => $gestor->nome,
                'email' => $gestor->email,
                'token' => $token,
                'tipo' => $gestor->tipo,
            ]
        ];
    }
    
    /**
     * POST /api/gestor/gestor-usuarios/create
     * Cadastro de novo gestor
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $dados = $request->post();
        
        // Validações obrigatórias
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
        
        // Cria novo gestor
        $gestor = new GestorUsuario();
        $gestor->nome = $dados['nome'];
        $gestor->email = $dados['email'];
        $gestor->setPassword($dados['senha']);
        $gestor->tipo = $dados['tipo'] ?? 'comercial'; // comercial, admin, etc
        $gestor->status = GestorUsuario::STATUS_ATIVO;
        
        // Campos opcionais
        if (!empty($dados['cpf'])) {
            $gestor->cpf = $dados['cpf'];
        }
        if (!empty($dados['telefone'])) {
            $gestor->telefone = $dados['telefone'];
        }
        
        // Gera token de acesso
        $gestor->generateAuthKey();
        $token = $gestor->generateAccessToken();
        
        if ($gestor->save()) {
            return [
                'success' => true,
                'message' => 'Gestor cadastrado com sucesso',
                'data' => [
                    'id' => $gestor->id,
                    'nome' => $gestor->nome,
                    'email' => $gestor->email,
                    'token' => $token,
                    'tipo' => $gestor->tipo,
                ]
            ];
        } else {
            // Erros de validação do model
            return [
                'success' => false,
                'message' => 'Erro ao cadastrar gestor',
                'errors' => $gestor->errors
            ];
        }
    }
    
    /**
     * POST /api/gestor/gestor-usuarios/logout
     * Logout do gestor (invalida token)
     */
    public function actionLogout()
    {
        $token = Yii::$app->request->post('token');
        
        if ($token) {
            $gestor = GestorUsuario::findIdentityByAccessToken($token);
            if ($gestor) {
                $gestor->access_token = null;
                $gestor->save(false);
            }
        }
        
        return [
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ];
    }
    
    /**
     * GET /api/gestor/gestor-usuarios/me
     * Dados do gestor logado (requer token)
     */
    public function actionMe()
    {
        $token = Yii::$app->request->get('token');
        
        if (!$token) {
            throw new UnauthorizedHttpException('Token não fornecido');
        }
        
        $gestor = GestorUsuario::findIdentityByAccessToken($token);
        
        if (!$gestor) {
            throw new UnauthorizedHttpException('Token inválido');
        }
        
        return [
            'success' => true,
            'data' => [
                'id' => $gestor->id,
                'nome' => $gestor->nome,
                'email' => $gestor->email,
                'tipo' => $gestor->tipo,
                'ultimo_login' => $gestor->ultimo_login_at,
            ]
        ];
    }
}