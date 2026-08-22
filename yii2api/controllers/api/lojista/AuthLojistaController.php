<?php

namespace app\controllers\api\lojista;

use Yii;
use app\controllers\api\lojista\LojistaControllerBase;
use app\components\ApiResponse;
use app\models\api\lojista\LojistaUsuario;
use app\models\api\app\Loja; 
use yii\web\UnauthorizedHttpException;

class AuthLojistaController extends LojistaControllerBase
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
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

    // -------------------------------------------------------------
    // 1. Envio de OTP
    // -------------------------------------------------------------
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
            ->where(['telefone' => $telefone, 'deletado_em' => null])
            ->one();

        if (!$lojista) {
            return ApiResponse::error('Lojista não encontrado para este telefone', 404);
        }

        $codigoOtp = rand(100000, 999999);
        $lojista->reset_token = (string)$codigoOtp;
        $lojista->reset_token_expira_em = date('Y-m-d H:i:s', time() + 300);
        $lojista->save(false);

        Yii::info("🔢 Código OTP para lojista {$telefone}: {$codigoOtp}", __METHOD__);

        return ApiResponse::success([
            'message'  => 'Código enviado com sucesso',
            'telefone' => $telefone,
            'debug_code' => $codigoOtp,
        ]);
    }

    // -------------------------------------------------------------
    // 2. Verificação OTP e login (DUMB MODE - aceita qualquer código)
    // -------------------------------------------------------------
    public function actionVerifyOtp()
    {
        $request = Yii::$app->request;
        $telefone = $request->getBodyParam('phone');
        $codigo   = $request->getBodyParam('code');
        $deviceId = $request->getBodyParam('device_id'); // 🔥 VINDO DO BODY
        $deviceToken = $request->getBodyParam('device_token');

        if (empty($telefone) || empty($codigo)) {
            return ApiResponse::error('Telefone e código são obrigatórios', 400);
        }

        $telefone = preg_replace('/\D/', '', $telefone);
        $codigo   = trim($codigo);

        if (strlen($codigo) !== 6 || !ctype_digit($codigo)) {
            return ApiResponse::error('Código deve ter 6 dígitos', 400);
        }

        $lojista = LojistaUsuario::find()
            ->where(['telefone' => $telefone, 'deletado_em' => null])
            ->one();

        if (!$lojista) {
            return ApiResponse::error('Lojista não encontrado', 404);
        }

        // 🔥 DUMB VALIDATION: qualquer código de 6 dígitos é aceito em desenvolvimento
        // TODO: Remover em produção e validar com reset_token

        // Limpa o token gerado
        $lojista->reset_token = null;
        $lojista->reset_token_expira_em = null;

        // 🔥 🔥 🔥 SALVA O DEVICE_ID SE FORNECIDO
        if (!empty($deviceId)) {
            $lojista->device_id = $deviceId;
            Yii::info("📱 Device ID salvo para lojista {$lojista->id}: $deviceId", __METHOD__);
        }

        // 🔥 🔥 🔥 SALVA O DEVICE_TOKEN (FCM) SE FORNECIDO
        if (!empty($deviceToken)) {
            $lojista->device_token = $deviceToken;
            Yii::info("📱 Device token salvo para lojista {$lojista->id}: " . substr($deviceToken, 0, 20) . '...', __METHOD__);
        }

        $accessToken  = $lojista->generateAccessToken(7200);
        $refreshToken = $lojista->generateRefreshToken(2592000);

        $lojista->ultimo_login_em = date('Y-m-d H:i:s');
        $lojista->save(false);

        $lojas = $this->getLojasDoLojista($lojista->id);

        return ApiResponse::success([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in'    => 7200,
            'token_type'    => 'Bearer',
            'lojista'       => $this->formatLojista($lojista),
            'lojas'         => $lojas,
        ], 'Autenticação realizada com sucesso');
    }

    // -------------------------------------------------------------
    // 3. Login com email/senha (fallback)
    // -------------------------------------------------------------
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $email   = $request->getBodyParam('email');
        $senha   = $request->getBodyParam('senha');
        $deviceId = $request->getBodyParam('device_id'); // 🔥 VINDO DO BODY
        $deviceToken = $request->getBodyParam('device_token');

        if (empty($email) || empty($senha)) {
            return ApiResponse::error('Email e senha são obrigatórios', 400);
        }

        $lojista = LojistaUsuario::find()
            ->where(['email' => $email, 'deletado_em' => null])
            ->one();

        if (!$lojista || !$lojista->validatePassword($senha)) {
            return ApiResponse::error('Email ou senha inválidos', 401);
        }

        if (!$lojista->isAtivo()) {
            return ApiResponse::error('Lojista inativo', 401);
        }

        // 🔥 🔥 🔥 SALVA O DEVICE_ID SE FORNECIDO
        if (!empty($deviceId)) {
            $lojista->device_id = $deviceId;
            Yii::info("📱 Device ID salvo para lojista {$lojista->id} via login: $deviceId", __METHOD__);
        }

        // 🔥 🔥 🔥 SALVA O DEVICE_TOKEN (FCM) SE FORNECIDO
        if (!empty($deviceToken)) {
            $lojista->device_token = $deviceToken;
            Yii::info("📱 Device token salvo para lojista {$lojista->id} via login", __METHOD__);
        }

        $accessToken  = $lojista->generateAccessToken(7200);
        $refreshToken = $lojista->generateRefreshToken(2592000);

        $lojista->ultimo_login_em = date('Y-m-d H:i:s');
        $lojista->ultimo_login_ip = Yii::$app->request->userIP;
        $lojista->save(false);

        $lojas = $this->getLojasDoLojista($lojista->id);

        return ApiResponse::success([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in'    => 7200,
            'token_type'    => 'Bearer',
            'lojista'       => $this->formatLojista($lojista),
            'lojas'         => $lojas,
        ], 'Login realizado com sucesso');
    }

    // -------------------------------------------------------------
    // 4. Refresh token
    // -------------------------------------------------------------
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
        $lojas = $this->getLojasDoLojista($lojista->id);

        return ApiResponse::success([
            'access_token'  => $novoAccessToken,
            'expires_in'    => 7200,
            'token_type'    => 'Bearer',
            'lojista'       => $this->formatLojista($lojista),
            'lojas'         => $lojas,
        ], 'Token renovado com sucesso');
    }

    // -------------------------------------------------------------
    // 5. Logout
    // -------------------------------------------------------------
    public function actionLogout()
    {
        try {
            $lojista = $this->getLojistaByToken();
            if ($lojista) {
                // 🔥 REMOVE O DEVICE_TOKEN E DEVICE_ID AO FAZER LOGOUT
                $lojista->device_token = null;
                $lojista->device_id = null;
                $lojista->save(false);
                Yii::info("📱 Device ID e token removidos para lojista {$lojista->id} (logout)", __METHOD__);
                
                $lojista->invalidateTokens();
            }
        } catch (\Exception $e) {
            Yii::info("Logout com token inválido/ausente", __METHOD__);
        }
        return ApiResponse::success(null, 'Logout realizado com sucesso');
    }

    // -------------------------------------------------------------
    // 6. Criação de novo lojista (público)
    // -------------------------------------------------------------
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $data = $request->bodyParams;

        if (empty($data['nome'])) {
            return ApiResponse::error('Nome é obrigatório', 400);
        }
        if (empty($data['email'])) {
            return ApiResponse::error('E-mail é obrigatório', 400);
        }
        $senha = $data['senha'] ?? $data['password'] ?? '';
        if (empty($senha)) {
            return ApiResponse::error('Senha é obrigatória', 400);
        }

        if (LojistaUsuario::find()->where(['email' => $data['email']])->exists()) {
            return ApiResponse::error('E-mail já cadastrado', 409);
        }
        if (!empty($data['cpf_cnpj']) && LojistaUsuario::find()->where(['cpf_cnpj' => $data['cpf_cnpj']])->exists()) {
            return ApiResponse::error('CPF/CNPJ já cadastrado', 409);
        }

        $usuario = new LojistaUsuario();
        $usuario->nome     = $data['nome'];
        $usuario->email    = $data['email'];
        $usuario->telefone = $data['telefone'] ?? null;
        $usuario->cpf_cnpj = $data['cpf_cnpj'] ?? null;
        $usuario->setPassword($senha);
        $usuario->generateAuthKey();
        $usuario->generateAccessToken();
        $usuario->funcao = $data['funcao'] ?? LojistaUsuario::FUNCAO_VENDEDOR;
        $usuario->status = LojistaUsuario::STATUS_ATIVO;

        if ($usuario->save()) {
            return ApiResponse::success([
                'id'           => $usuario->id,
                'nome'         => $usuario->nome,
                'email'        => $usuario->email,
                'telefone'     => $usuario->telefone,
                'cpf_cnpj'     => $usuario->cpf_cnpj,
                'funcao'       => $usuario->funcao,
                'status'       => $usuario->status,
                'access_token' => $usuario->access_token,
            ], 'Lojista criado com sucesso');
        } else {
            return ApiResponse::error('Erro ao criar lojista: ' . json_encode($usuario->errors), 500);
        }
    }

    // -------------------------------------------------------------
    //  MÉTODOS AUXILIARES
    // -------------------------------------------------------------

    private function getLojasDoLojista($usuarioId)
    {
        return Loja::find()
            ->select([
                'loja.id', 
                'loja.nome', 
                'loja.logradouro', 
                'loja.numero',
                'loja.complemento', 
                'loja.bairro', 
                'loja.cidade',
                'loja.uf AS estado',
                'loja.cep', 
                'loja.telefone'
            ])
            ->innerJoin('store_usuario_loja sul', 'sul.loja_id = loja.id')
            ->where(['sul.usuario_id' => $usuarioId, 'sul.status' => 1])
            ->andWhere(['loja.deletado_em' => null])
            ->asArray()
            ->all();
    }

    private function formatLojista(LojistaUsuario $lojista)
    {
        return [
            'id'            => $lojista->id,
            'nome'          => $lojista->nome,
            'email'         => $lojista->email,
            'telefone'      => $lojista->telefone,
            'cpf_cnpj'      => $lojista->cpf_cnpj,
            'status'        => $lojista->status,
            'funcao'        => $lojista->funcao,
            'ultimo_login_em' => $lojista->ultimo_login_em,
            'criado_em'     => $lojista->criado_em,
            'device_id'     => $lojista->device_id,
            'device_token'  => $lojista->device_token,
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
            throw new UnauthorizedHttpException('Lojista não autenticado');
        }

        return $lojista;
    }
}