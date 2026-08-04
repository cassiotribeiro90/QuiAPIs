<?php

namespace app\controllers\api\app;

use Yii;
use app\controllers\api\app\AppControllerBase;
use app\models\api\app\Usuario;
use app\models\api\app\AppEndereco;
use app\components\ApiResponse;
use GuzzleHttp\Client;
use yii\db\Exception;

class AuthController extends AppControllerBase
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'login',
                'cadastro',
                'social',
                'refresh-token',
                'convidado',
                'phone',
                'verify-otp',
            ];
        }
        
        return $behaviors;
    }

    // ==================== NOVOS ENDPOINTS OTP ====================

    /**
     * POST /api/app/auth/phone
     * Recebe telefone, vincula ao convidado existente ou cria novo
     */
    public function actionPhone()
    {
        $request = Yii::$app->request;
        $telefone = $request->post('phone');
        $deviceId = $request->getHeaders()->get('X-Device-Id');

        if (empty($telefone)) {
            return ApiResponse::error('Telefone é obrigatório', 400);
        }

        $telefone = preg_replace('/\D/', '', $telefone);

        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            return ApiResponse::error('Telefone inválido', 400);
        }

        // 1. Busca por telefone
        $usuario = Usuario::find()
            ->where(['telefone' => $telefone])
            ->andWhere(['deletado_em' => null])
            ->one();

        // 2. Se não encontrou por telefone, busca convidado pelo device_id
        if (!$usuario && $deviceId) {
            $usuario = Usuario::find()
                ->where(['device_id' => $deviceId, 'status' => 'convidado'])
                ->andWhere(['deletado_em' => null])
                ->one();
            
            if ($usuario) {
                $usuario->telefone = $telefone;
                $usuario->save(false);
                Yii::info("✅ Telefone vinculado ao convidado ID {$usuario->id}", __METHOD__);
            }
        }

        // 3. Se ainda não encontrou, cria novo usuário
        if (!$usuario) {
            $usuario = new Usuario();
            $usuario->telefone = $telefone;
            $usuario->device_id = $deviceId;
            $usuario->nome = null;
            $usuario->email = null;
            $usuario->auth_key = Yii::$app->security->generateRandomString(32);
            $usuario->tipo = Usuario::TIPO_CLIENTE;
            $usuario->status = Usuario::STATUS_ATIVO;
            $usuario->pref_tema = 'auto';
            $usuario->save(false);

            Yii::info("✅ Novo usuário criado via telefone: ID {$usuario->id}", __METHOD__);
        }

        // Mock: gera código OTP
        $codigoOtp = rand(100000, 999999);
        Yii::info("🔢 Código OTP para {$telefone}: {$codigoOtp}", __METHOD__);

        $usuario->reset_token = (string)$codigoOtp;
        $usuario->reset_token_expira_em = date('Y-m-d H:i:s', time() + 300);
        $usuario->save(false);

        return ApiResponse::success([
            'message' => 'Código enviado com sucesso',
            'telefone' => $telefone,
        ]);
    }

    /**
     * POST /api/app/auth/verify-otp
     * Verifica código OTP (mock: qualquer código de 6 dígitos)
     */
    public function actionVerifyOtp()
    {
        $request = Yii::$app->request;
        $telefone = $request->post('phone');
        $codigo = $request->post('code');
        $deviceId = $request->getHeaders()->get('X-Device-Id');

        if (empty($telefone) || empty($codigo)) {
            return ApiResponse::error('Telefone e código são obrigatórios', 400);
        }

        $telefone = preg_replace('/\D/', '', $telefone);
        $codigo = trim($codigo);

        // 1. Busca por telefone
        $usuario = Usuario::find()
            ->where(['telefone' => $telefone])
            ->andWhere(['deletado_em' => null])
            ->one();

        // 2. Se não encontrou, busca convidado pelo device_id
        if (!$usuario && $deviceId) {
            $usuario = Usuario::find()
                ->where(['device_id' => $deviceId, 'status' => 'convidado'])
                ->andWhere(['deletado_em' => null])
                ->one();
            
            if ($usuario) {
                $usuario->telefone = $telefone;
                $usuario->status = Usuario::STATUS_ATIVO;
                Yii::info("✅ Convidado ID {$usuario->id} ativado com telefone", __METHOD__);
            }
        }

        // 3. Se ainda não encontrou, erro
        if (!$usuario) {
            return ApiResponse::error('Usuário não encontrado', 404);
        }

        // Mock: aceita qualquer código de 6 dígitos
        if (strlen($codigo) !== 6 || !ctype_digit($codigo)) {
            return ApiResponse::error('Código deve ter 6 dígitos', 400);
        }

        // Limpa o código
        $usuario->reset_token = null;
        $usuario->reset_token_expira_em = null;

        // Garante que o device_id está salvo
        if (empty($usuario->device_id) && $deviceId) {
            $usuario->device_id = $deviceId;
        }

        // Gera tokens
        $accessToken = $usuario->generateAccessToken(7200);
        $refreshToken = $usuario->generateRefreshToken(2592000);

        $usuario->ultimo_login_em = date('Y-m-d H:i:s');
        $usuario->login_count = ($usuario->login_count ?? 0) + 1;
        $usuario->save(false);

        $enderecoPadrao = $this->getEnderecoPadrao($usuario->id);

        return ApiResponse::success([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 7200,
            'token_type' => 'Bearer',
            'usuario' => $this->formatUsuario($usuario),
            'endereco' => $enderecoPadrao,
            'nome_preenchido' => !empty($usuario->nome),
        ], 'Autenticação realizada com sucesso');
    }

    // ==================== ENDPOINTS EXISTENTES ====================

    /**
     * POST /api/app/auth/convidado
     * Cria ou recupera um usuário convidado
     */
    public function actionConvidado()
    {
        $request = Yii::$app->request;
        
        $deviceId = $request->getBodyParam('device_id') ?? 
                    $request->getHeaders()->get('X-Device-Id');
        
        if (!$deviceId) {
            $deviceId = md5(Yii::$app->request->userIP . Yii::$app->request->userAgent);
        }

        $usuario = Usuario::find()
            ->where(['device_id' => $deviceId])
            ->andWhere(['status' => 'convidado'])
            ->one();

        if (!$usuario) {
            $usuario = new Usuario();
            $usuario->device_id = $deviceId;
            $usuario->status = 'convidado';
            $usuario->nome = null;
            $usuario->email = null;
            $usuario->cpf = null;
            $usuario->telefone = null;
            $usuario->auth_key = Yii::$app->security->generateRandomString(32);
            $usuario->tipo = Usuario::TIPO_CLIENTE;
            $usuario->pref_tema = 'auto';
            $usuario->save(false);
        }

        $token = Yii::$app->security->generateRandomString(64);
        $usuario->access_token = $token;
        $usuario->access_token_expira_em = date('Y-m-d H:i:s', time() + 7200);
        $usuario->save(false);

        return ApiResponse::success([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'cpf' => $usuario->cpf,
                'telefone' => $usuario->telefone,
                'status' => $usuario->status,
                'device_id' => $usuario->device_id,
            ],
            'tipo' => 'convidado',
        ]);
    }

    /**
     * POST /api/app/auth/login
     * Login do cliente (email/senha) - mantido para compatibilidade
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $email = $request->post('email');
        $senha = $request->post('senha');
        
        if (empty($email) || empty($senha)) {
            return ApiResponse::error('Email e senha são obrigatórios', 400);
        }
        
        $usuario = Usuario::findByEmail($email);
        
        if (!$usuario || !$usuario->validatePassword($senha)) {
            sleep(1);
            return ApiResponse::error('Email ou senha inválidos', 401);
        }
        
        if (!$usuario->isAtivo()) {
            return ApiResponse::error('Usuário inativo', 401);
        }
        
        $this->updateLoginMetadata($usuario, 'email');
        
        return ApiResponse::success(
            $this->formatUserWithTokensAndEndereco($usuario),
            'Login realizado com sucesso'
        );
    }

    /**
     * POST /api/app/auth/cadastro
     * Cadastro completo de novo cliente - mantido para compatibilidade
     */
    public function actionCadastro()
    {
        $request = Yii::$app->request;
        $dados = $request->post();
        
        $camposPessoais = ['nome', 'email', 'senha', 'confirmar_senha'];
        foreach ($camposPessoais as $campo) {
            if (empty($dados[$campo])) {
                return ApiResponse::error("O campo '$campo' é obrigatório", 400);
            }
        }
        
        $email = trim($dados['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ApiResponse::error('Email inválido', 400);
        }
        
        if (Usuario::find()->where(['email' => $email])->exists()) {
            return ApiResponse::error('Este email já está cadastrado', 409);
        }
        
        $senha = $dados['senha'];
        if (strlen($senha) < 6) {
            return ApiResponse::error('A senha deve ter pelo menos 6 caracteres', 400);
        }
        if ($senha !== $dados['confirmar_senha']) {
            return ApiResponse::error('As senhas não coincidem', 400);
        }
        
        $telefone = null;
        if (!empty($dados['telefone'])) {
            $telefone = preg_replace('/\D/', '', $dados['telefone']);
            if (strlen($telefone) < 10 || strlen($telefone) > 11) {
                return ApiResponse::error('Telefone inválido', 400);
            }
        }
            
        if (!isset($dados['termos_aceitos']) || !in_array($dados['termos_aceitos'], [0, 1], true)) {
            return ApiResponse::error('É necessário aceitar os termos de uso', 400);
        }
        $termosAceitos = (int)$dados['termos_aceitos'];
        
        if (empty($dados['endereco']) || !is_array($dados['endereco'])) {
            return ApiResponse::error('Endereço de entrega é obrigatório', 400);
        }
        
        $enderecoData = $dados['endereco'];
        $camposEndereco = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'];
        foreach ($camposEndereco as $campo) {
            if (empty($enderecoData[$campo])) {
                return ApiResponse::error("O campo de endereço '$campo' é obrigatório", 400);
            }
        }
        
        $cep = preg_replace('/\D/', '', $enderecoData['cep']);
        if (strlen($cep) !== 8) {
            return ApiResponse::error('CEP inválido', 400);
        }
        
        $uf = strtoupper(trim($enderecoData['uf']));
        if (strlen($uf) !== 2) {
            return ApiResponse::error('UF inválida', 400);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $deviceId = $request->post('device_id');
            if ($deviceId) {
                $usuario = Usuario::find()
                    ->where(['device_id' => $deviceId, 'status' => 'convidado'])
                    ->one();
                if ($usuario) {
                    $usuario->nome = trim($dados['nome']);
                    $usuario->email = $email;
                    $usuario->telefone = $telefone;
                    $usuario->setPassword($senha);
                    $usuario->status = Usuario::STATUS_ATIVO;
                    $usuario->termos_aceitos = $termosAceitos;
                    $usuario->termos_aceitos_em = date('Y-m-d H:i:s');
                    if (!empty($dados['cpf'])) {
                        $usuario->cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
                    }
                    if (!$usuario->save()) {
                        throw new \Exception('Erro ao atualizar usuário: ' . json_encode($usuario->errors));
                    }
                }
            }

            if (!isset($usuario)) {
                $usuario = new Usuario();
                $usuario->nome = trim($dados['nome']);
                $usuario->email = $email;
                $usuario->telefone = $telefone;
                $usuario->setPassword($senha);
                $usuario->generateAuthKey();
                $usuario->status = Usuario::STATUS_ATIVO;
                $usuario->termos_aceitos = $termosAceitos;
                $usuario->termos_aceitos_em = date('Y-m-d H:i:s');
                if (!empty($dados['cpf'])) {
                    $usuario->cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
                }
                if (!$usuario->save()) {
                    throw new \Exception('Erro ao salvar usuário: ' . json_encode($usuario->errors));
                }
            }
            
            $endereco = new AppEndereco();
            $endereco->usuario_id = $usuario->id;
            $endereco->cep = $cep;
            $endereco->logradouro = trim($enderecoData['logradouro']);
            $endereco->numero = trim($enderecoData['numero']);
            $endereco->complemento = isset($enderecoData['complemento']) ? trim($enderecoData['complemento']) : null;
            $endereco->bairro = trim($enderecoData['bairro']);
            $endereco->cidade = trim($enderecoData['cidade']);
            $endereco->uf = $uf;
            $endereco->referencia = isset($enderecoData['referencia']) ? trim($enderecoData['referencia']) : null;
            $endereco->destinatario = $usuario->nome;
            $endereco->latitude = isset($enderecoData['latitude']) ? (float)$enderecoData['latitude'] : null;
            $endereco->longitude = isset($enderecoData['longitude']) ? (float)$enderecoData['longitude'] : null;
            $endereco->tipo = 'entrega';
            $endereco->padrao = 1;
            $endereco->ativo = 1;
            
            if (!$endereco->save()) {
                throw new \Exception('Erro ao salvar endereço: ' . json_encode($endereco->errors));
            }
            
            if ($endereco->latitude === null || $endereco->longitude === null) {
                $this->enriquecerCoordenadas($endereco);
                $endereco->save(false);
            }
            
            $transaction->commit();
            
            $this->updateLoginMetadata($usuario, 'email');
            
            return ApiResponse::success(
                $this->formatUserWithTokensAndEndereco($usuario),
                'Cadastro realizado com sucesso',
                201
            );
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Erro no cadastro: ' . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao processar cadastro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/app/auth/social
     */
    public function actionSocial()
    {
        $request = Yii::$app->request;
        $provider = $request->post('provider');
        $token = $request->post('token');
        $additionalData = $request->post('additionalData', []);
        
        if (empty($provider) || empty($token)) {
            return ApiResponse::error('Provider e token são obrigatórios', 400);
        }
        
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            return ApiResponse::error('Provider não suportado', 400);
        }
        
        try {
            $socialUser = $this->validateSocialToken($provider, $token, $additionalData);
            $usuario = $this->findOrCreateSocialUser($provider, $socialUser);
            $this->updateLoginMetadata($usuario, $provider);
            
            return ApiResponse::success(
                $this->formatUserWithTokensAndEndereco($usuario),
                'Login social realizado com sucesso'
            );
        } catch (\Exception $e) {
            Yii::error("Social login error ({$provider}): " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Token inválido ou expirado', 401);
        }
    }

    /**
     * POST /api/app/auth/refresh-token
     */
    public function actionRefreshToken()
    {
        $refreshToken = Yii::$app->request->post('refresh_token');
        
        if (empty($refreshToken)) {
            return ApiResponse::error('Refresh token é obrigatório', 400);
        }
        
        $usuario = Usuario::findByRefreshToken($refreshToken);
        
        if (!$usuario) {
            return ApiResponse::error('Refresh token inválido ou expirado', 401);
        }
        
        if (!$usuario->isAtivo()) {
            return ApiResponse::error('Usuário inativo', 401);
        }
        
        $novoAccessToken = $usuario->generateAccessToken();
        $enderecoPadrao = $this->getEnderecoPadrao($usuario->id);
        
        return ApiResponse::success([
            'access_token' => $novoAccessToken,
            'expires_in' => 7200,
            'token_type' => 'Bearer',
            'usuario' => $this->formatUsuario($usuario),
            'endereco' => $enderecoPadrao,
        ], 'Token renovado com sucesso');
    }

    /**
     * POST /api/app/auth/logout
     */
    public function actionLogout()
    {
        try {
            $usuario = $this->getUserByToken();
            $usuario->invalidateTokens();
        } catch (\Exception $e) {
            Yii::info("Logout com token inválido/ausente", __METHOD__);
        }
        return ApiResponse::success(null, 'Logout realizado com sucesso');
    }

    /**
     * GET /api/app/auth/me
     */
    public function actionMe()
    {
        $usuario = $this->getUserByToken();
        $enderecoPadrao = $this->getEnderecoPadrao($usuario->id);
        
        return ApiResponse::success([
            'usuario' => $this->formatUsuario($usuario),
            'endereco' => $enderecoPadrao,
        ]);
    }

    /**
     * PUT /api/app/auth/me
     * Atualiza perfil do usuário (nome, email, whatsapp)
     */
    public function actionUpdateMe()
    {
        $usuario = $this->getUserByToken();
        $request = Yii::$app->request;
        
        $nome = $request->post('nome');
        $email = $request->post('email');
        $whatsapp = $request->post('whatsapp');
        
        if (empty($nome)) {
            return ApiResponse::error('Nome é obrigatório', 400);
        }
        
        $usuario->nome = trim($nome);
        
        if (!empty($email)) {
            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ApiResponse::error('Email inválido', 400);
            }
            $existente = Usuario::find()
                ->where(['email' => $email])
                ->andWhere(['!=', 'id', $usuario->id])
                ->andWhere(['deletado_em' => null])
                ->one();
            if ($existente) {
                return ApiResponse::error('Este email já está cadastrado', 409);
            }
            $usuario->email = $email;
        }
        
        if (!empty($whatsapp)) {
            $usuario->whatsapp = preg_replace('/\D/', '', $whatsapp);
        }
        
        if ($usuario->save()) {
            return ApiResponse::success([
                'usuario' => $this->formatUsuario($usuario),
            ], 'Perfil atualizado com sucesso');
        }
        
        return ApiResponse::error('Erro ao atualizar perfil', 500);
    }

    // ==================== MÉTODOS AUXILIARES ====================

    private function formatUsuario(Usuario $usuario)
    {
        return [
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'telefone' => $usuario->telefone,
            'whatsapp' => $usuario->whatsapp,
            'cpf' => $usuario->cpf,
            'avatar' => $usuario->avatar,
            'ultimo_login_em' => $usuario->ultimo_login_em,
            'criado_em' => $usuario->criado_em,
        ];
    }

    private function getEnderecoPadrao($usuarioId)
    {
        $endereco = AppEndereco::find()
            ->where(['usuario_id' => $usuarioId, 'padrao' => 1, 'ativo' => 1, 'deletado_em' => null])
            ->one();
            
        if (!$endereco) {
            return null;
        }
        
        return [
            'id' => $endereco->id,
            'apelido' => $endereco->apelido,
            'cep' => $endereco->cep,
            'logradouro' => $endereco->logradouro,
            'numero' => $endereco->numero,
            'complemento' => $endereco->complemento,
            'bairro' => $endereco->bairro,
            'cidade' => $endereco->cidade,
            'uf' => $endereco->uf,
            'latitude' => $endereco->latitude,
            'longitude' => $endereco->longitude,
            'referencia' => $endereco->referencia,
            'destinatario' => $endereco->destinatario,
            'telefone_contato' => $endereco->telefone_contato,
        ];
    }

    private function formatUserWithTokensAndEndereco(Usuario $usuario)
    {
        $enderecoPadrao = $this->getEnderecoPadrao($usuario->id);
        
        return [
            'access_token' => $usuario->generateAccessToken(),
            'refresh_token' => $usuario->generateRefreshToken(),
            'expires_in' => 7200,
            'token_type' => 'Bearer',
            'usuario' => $this->formatUsuario($usuario),
            'endereco' => $enderecoPadrao,
        ];
    }

    private function updateLoginMetadata(Usuario $usuario, $provider)
    {
        $usuario->ultimo_login_em = date('Y-m-d H:i:s');
        $usuario->ultimo_login_ip = Yii::$app->request->userIP;
        $usuario->login_count = ($usuario->login_count ?? 0) + 1;
        $usuario->ultimo_login_provider = $provider;
        $usuario->save(false);
    }

    private function validateSocialToken($provider, $token, $additionalData = [])
    {
        $client = new Client(['timeout' => 10]);
        
        switch ($provider) {
            case 'google':
                $response = $client->get("https://www.googleapis.com/oauth2/v3/userinfo", [
                    'headers' => ['Authorization' => "Bearer {$token}"]
                ]);
                $data = json_decode($response->getBody(), true);
                if (!isset($data['sub'])) {
                    throw new \Exception('Token Google inválido');
                }
                return [
                    'id' => $data['sub'],
                    'nome' => $data['name'] ?? $additionalData['name'] ?? 'Usuário Google',
                    'email' => $data['email'] ?? $additionalData['email'] ?? null,
                    'avatar' => $data['picture'] ?? null,
                ];
                
            case 'facebook':
                $response = $client->get("https://graph.facebook.com/me", [
                    'query' => [
                        'fields' => 'id,name,email,picture.type(large)',
                        'access_token' => $token
                    ]
                ]);
                $data = json_decode($response->getBody(), true);
                if (!isset($data['id'])) {
                    throw new \Exception('Token Facebook inválido');
                }
                return [
                    'id' => $data['id'],
                    'nome' => $data['name'] ?? $additionalData['name'] ?? 'Usuário Facebook',
                    'email' => $data['email'] ?? $additionalData['email'] ?? null,
                    'avatar' => $data['picture']['data']['url'] ?? null,
                ];
                
            case 'apple':
                return $this->validateAppleToken($token, $additionalData);
                
            default:
                throw new \Exception('Provider não suportado');
        }
    }

    private function validateAppleToken($identityToken, $additionalData = [])
    {
        $parts = explode('.', $identityToken);
        if (count($parts) !== 3) {
            throw new \Exception('Token Apple inválido');
        }
        
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        
        if ($payload['iss'] !== 'https://appleid.apple.com') {
            throw new \Exception('Issuer inválido');
        }
        
        return [
            'id' => $payload['sub'],
            'nome' => $additionalData['name'] ?? 'Usuário Apple',
            'email' => $payload['email'] ?? $additionalData['email'] ?? null,
            'avatar' => null,
        ];
    }

    private function findOrCreateSocialUser($provider, array $socialUser)
    {
        $idField = $provider . '_id';
        
        $usuario = Usuario::find()->where([$idField => $socialUser['id']])->one();
        
        if (!$usuario && !empty($socialUser['email'])) {
            $usuario = Usuario::findByEmail($socialUser['email']);
        }
        
        if (!$usuario) {
            $usuario = new Usuario();
            $usuario->status = Usuario::STATUS_ATIVO;
            $usuario->termos_aceitos = 1;
            $usuario->termos_aceitos_em = date('Y-m-d H:i:s');
            $usuario->generateAuthKey();
        }
        
        $usuario->nome = $socialUser['nome'] ?: $usuario->nome ?: 'Usuário ' . ucfirst($provider);
        if (!empty($socialUser['email'])) {
            $usuario->email = $socialUser['email'];
        }
        $usuario->avatar = $socialUser['avatar'] ?? $usuario->avatar;
        $usuario->$idField = $socialUser['id'];
        
        if (!$usuario->save()) {
            throw new \Exception('Erro ao salvar usuário: ' . json_encode($usuario->errors));
        }
        
        return $usuario;
    }

    private function getUserByToken()
    {
        $token = Yii::$app->request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);
        
        $usuario = Usuario::find()->where(['access_token' => $token])->one();
        
        if (!$usuario || !($usuario->isAtivo() || $usuario->status === 'convidado')) {
            throw new \yii\web\UnauthorizedHttpException('Usuário não autenticado');
        }
        
        return $usuario;
    }

    private function enriquecerCoordenadas(AppEndereco $endereco)
    {
        try {
            $client = new Client(['timeout' => 5]);
            $query = "{$endereco->logradouro}, {$endereco->numero}, {$endereco->bairro}, {$endereco->cidade}, {$endereco->uf}, Brasil";
            
            $response = $client->get('https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'QuiPede/1.0 (contato@quipede.com.br)',
                ],
            ]);
            
            $data = json_decode($response->getBody(), true);
            if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                $endereco->latitude = (float)$data[0]['lat'];
                $endereco->longitude = (float)$data[0]['lon'];
            }
        } catch (\Exception $e) {
            // Silencioso
        }
    }
}