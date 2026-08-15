<?php

namespace app\controllers\api\lojista;

use Yii;
use app\controllers\api\lojista\LojistaControllerBase;
use app\components\ApiResponse;
use app\models\api\lojista\LojistaUsuario;

class AuthLojistaController extends LojistaControllerBase
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // 🔥 Ações públicas (não precisam de autenticação)
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'options',
                'phone',
                'verify-otp',
                'login',
                'refresh-token',
                'create',
            ];
        }

        return $behaviors;
    }

    /**
     * POST /api/lojista/auth/phone
     * Envia OTP para o telefone do lojista
     */
    public function actionPhone()
    {
        $request = Yii::$app->request;
        $telefone = $request->getBodyParam('phone');

        if (empty($telefone)) {
            return ApiResponse::error('Telefone é obrigatório', 400);
        }

        $telefone = preg_replace('/\D/', '', $telefone);

        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            return ApiResponse::error('Telefone inválido', 400);
        }

        $lojista = LojistaUsuario::find()
            ->where(['telefone' => $telefone])
            ->andWhere(['deletado_em' => null])
            ->one();

        if (!$lojista) {
            return ApiResponse::error('Lojista não encontrado para este telefone', 404);
        }

        // Gera código OTP (mock - em produção, enviar por SMS)
        $codigoOtp = rand(100000, 999999);
        $lojista->reset_token = (string)$codigoOtp;
        $lojista->reset_token_expira_em = date('Y-m-d H:i:s', time() + 300);
        $lojista->save(false);

        Yii::info("🔢 Código OTP para lojista {$telefone}: {$codigoOtp}", __METHOD__);

        return ApiResponse::success([
            'message' => 'Código enviado com sucesso',
            'telefone' => $telefone,
        ]);
    }

    /**
     * POST /api/lojista/auth/verify-otp
     * Verifica OTP e autentica o lojista
     */
    public function actionVerifyOtp()
    {
        $request = Yii::$app->request;
        $telefone = $request->getBodyParam('phone');
        $codigo = $request->getBodyParam('code');
        $deviceId = $request->getHeaders()->get('X-Device-Id');

        if (empty($telefone) || empty($codigo)) {
            return ApiResponse::error('Telefone e código são obrigatórios', 400);
        }

        $telefone = preg_replace('/\D/', '', $telefone);
        $codigo = trim($codigo);

        if (strlen($codigo) !== 6 || !ctype_digit($codigo)) {
            return ApiResponse::error('Código deve ter 6 dígitos', 400);
        }

        $lojista = LojistaUsuario::find()
            ->where(['telefone' => $telefone])
            ->andWhere(['deletado_em' => null])
            ->one();

        if (!$lojista) {
            return ApiResponse::error('Lojista não encontrado', 404);
        }

        // 🔥 VALIDAÇÃO DUMB (desenvolvimento) - qualquer código 6 dígitos funciona
        // TODO: Remover em produção e validar com reset_token

        $lojista->reset_token = null;
        $lojista->reset_token_expira_em = null;

        if (empty($lojista->device_id) && $deviceId) {
            $lojista->device_id = $deviceId;
        }

        $accessToken = $lojista->generateAccessToken(7200);
        $refreshToken = $lojista->generateRefreshToken(2592000);

        $lojista->ultimo_login_em = date('Y-m-d H:i:s');
        // 🔥 REMOVIDO: $lojista->login_count = ($lojista->login_count ?? 0) + 1;
        $lojista->save(false);

        return ApiResponse::success([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 7200,
            'token_type' => 'Bearer',
            'lojista' => $this->formatLojista($lojista),
        ], 'Autenticação realizada com sucesso');
    }

    /**
     * POST /api/lojista/auth/login
     * Login com email e senha (fallback)
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $email = $request->getBodyParam('email');
        $senha = $request->getBodyParam('senha');

        if (empty($email) || empty($senha)) {
            return ApiResponse::error('Email e senha são obrigatórios', 400);
        }

        $lojista = LojistaUsuario::find()
            ->where(['email' => $email])
            ->andWhere(['deletado_em' => null])
            ->one();

        if (!$lojista || !$lojista->validatePassword($senha)) {
            return ApiResponse::error('Email ou senha inválidos', 401);
        }

        if (!$lojista->isAtivo()) {
            return ApiResponse::error('Lojista inativo', 401);
        }

        $accessToken = $lojista->generateAccessToken(7200);
        $refreshToken = $lojista->generateRefreshToken(2592000);

        $lojista->ultimo_login_em = date('Y-m-d H:i:s');
        $lojista->ultimo_login_ip = Yii::$app->request->userIP;
        // 🔥 REMOVIDO: $lojista->login_count = ($lojista->login_count ?? 0) + 1;
        $lojista->save(false);

        return ApiResponse::success([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 7200,
            'token_type' => 'Bearer',
            'lojista' => $this->formatLojista($lojista),
        ], 'Login realizado com sucesso');
    }

    /**
     * POST /api/lojista/auth/refresh-token
     * Renova o access token
     */
    public function actionRefreshToken()
    {
        $refreshToken = Yii::$app->request->getBodyParam('refresh_token');

        if (empty($refreshToken)) {
            return ApiResponse::error('Refresh token é obrigatório', 400);
        }

        $lojista = LojistaUsuario::find()
            ->where(['refresh_token' => $refreshToken])
            ->andWhere(['>', 'refresh_token_expira_em', date('Y-m-d H:i:s')])
            ->andWhere(['deletado_em' => null])
            ->one();

        if (!$lojista) {
            return ApiResponse::error('Refresh token inválido ou expirado', 401);
        }

        if (!$lojista->isAtivo()) {
            return ApiResponse::error('Lojista inativo', 401);
        }

        $novoAccessToken = $lojista->generateAccessToken();

        return ApiResponse::success([
            'access_token' => $novoAccessToken,
            'expires_in' => 7200,
            'token_type' => 'Bearer',
            'lojista' => $this->formatLojista($lojista),
        ], 'Token renovado com sucesso');
    }

    /**
     * POST /api/lojista/auth/logout
     * Invalida os tokens do lojista
     */
    public function actionLogout()
    {
        try {
            $lojista = $this->getLojistaByToken();
            if ($lojista) {
                $lojista->invalidateTokens();
            }
        } catch (\Exception $e) {
            Yii::info("Logout com token inválido/ausente", __METHOD__);
        }
        return ApiResponse::success(null, 'Logout realizado com sucesso');
    }

    /**
     * POST /api/lojista/auth/create
     * Cria um novo lojista (público)
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $data = $request->bodyParams;

        // Validações básicas
        if (empty($data['nome'])) {
            return ApiResponse::error('Nome é obrigatório', 400);
        }
        if (empty($data['email'])) {
            return ApiResponse::error('E-mail é obrigatório', 400);
        }
        if (empty($data['senha']) && empty($data['password'])) {
            return ApiResponse::error('Senha é obrigatória', 400);
        }

        $senha = $data['senha'] ?? $data['password'] ?? '';

        // Verificar se e-mail já existe
        if (LojistaUsuario::find()->where(['email' => $data['email']])->exists()) {
            return ApiResponse::error('E-mail já cadastrado', 409);
        }

        // Verificar se CPF já existe (se fornecido)
        if (!empty($data['cpf_cnpj']) && LojistaUsuario::find()->where(['cpf_cnpj' => $data['cpf_cnpj']])->exists()) {
            return ApiResponse::error('CPF/CNPJ já cadastrado', 409);
        }

        // Criar usuário
        $usuario = new LojistaUsuario();
        $usuario->nome = $data['nome'];
        $usuario->email = $data['email'];
        $usuario->telefone = $data['telefone'] ?? null;
        $usuario->cpf_cnpj = $data['cpf_cnpj'] ?? null;
        $usuario->setPassword($senha);
        $usuario->generateAuthKey();
        $usuario->generateAccessToken();
        $usuario->funcao = $data['funcao'] ?? LojistaUsuario::FUNCAO_VENDEDOR;
        $usuario->status = LojistaUsuario::STATUS_ATIVO;

        if ($usuario->save()) {
            return ApiResponse::success([
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'telefone' => $usuario->telefone,
                'cpf_cnpj' => $usuario->cpf_cnpj,
                'funcao' => $usuario->funcao,
                'status' => $usuario->status,
                'access_token' => $usuario->access_token,
            ], 'Lojista criado com sucesso');
        } else {
            return ApiResponse::error('Erro ao criar lojista: ' . json_encode($usuario->errors), 500);
        }
    }

    // ==================== MÉTODOS AUXILIARES ====================

    private function formatLojista(LojistaUsuario $lojista)
    {
        return [
            'id' => $lojista->id,
            'nome' => $lojista->nome,
            'email' => $lojista->email,
            'telefone' => $lojista->telefone,
            'cpf_cnpj' => $lojista->cpf_cnpj,
            'status' => $lojista->status,
            'funcao' => $lojista->funcao,
            'ultimo_login_em' => $lojista->ultimo_login_em,
            'criado_em' => $lojista->criado_em,
        ];
    }

    private function getLojistaByToken()
    {
        $token = Yii::$app->request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);

        $lojista = LojistaUsuario::find()
            ->where(['access_token' => $token])
            ->andWhere(['>', 'access_token_expira_em', date('Y-m-d H:i:s')])
            ->andWhere(['deletado_em' => null])
            ->one();

        if (!$lojista || !$lojista->isAtivo()) {
            throw new \yii\web\UnauthorizedHttpException('Lojista não autenticado');
        }

        return $lojista;
    }
}