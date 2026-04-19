<?php
// controllers/api/app/AuthController.php

namespace app\controllers\api\app;

use Yii;
use app\controllers\api\app\AppControllerBase;
use app\models\api\app\Usuario;
use app\models\api\app\AppEndereco;
use GuzzleHttp\Client;
use yii\db\Exception;

class AuthController extends AppControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     * Remove autenticação das ações públicas
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'login',
                'cadastro',
                'social',
                'refresh-token',
            ];
        }
        
        return $behaviors;
    }

    /**
     * POST /api/app/auth/login
     * Login do cliente (email/senha)
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
            sleep(1);
            return $this->errorResponse('Email ou senha inválidos', 401);
        }
        
        if (!$usuario->isAtivo()) {
            return $this->errorResponse('Usuário inativo', 401);
        }
        
        $this->updateLoginMetadata($usuario, 'email');
        
        return $this->successResponse(
            $this->formatUserWithTokens($usuario),
            'Login realizado com sucesso'
        );
    }

    /**
     * POST /api/app/auth/cadastro
     * Cadastro completo de novo cliente (dados pessoais + endereço)
     * 
     * Espera JSON com:
     * - nome, email, senha, confirmar_senha (obrigatórios)
     * - telefone (opcional)
     * - termos_aceitos (0 ou 1, obrigatório)
     * - endereco: { cep, logradouro, numero, complemento?, bairro, cidade, uf, referencia?, latitude?, longitude? }
     */
    public function actionCadastro()
    {
        $request = Yii::$app->request;
        $dados = $request->post();
        
        // ========== VALIDAÇÃO DOS DADOS PESSOAIS ==========
        $camposPessoais = ['nome', 'email', 'senha', 'confirmar_senha'];
        foreach ($camposPessoais as $campo) {
            if (empty($dados[$campo])) {
                return $this->errorResponse("O campo '$campo' é obrigatório", 400);
            }
        }
        
        // Valida email
        $email = trim($dados['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Email inválido', 400);
        }
        
        // Verifica se email já existe
        if (Usuario::find()->where(['email' => $email])->exists()) {
            return $this->errorResponse('Este email já está cadastrado', 409);
        }
        
        // Valida senha
        $senha = $dados['senha'];
        if (strlen($senha) < 6) {
            return $this->errorResponse('A senha deve ter pelo menos 6 caracteres', 400);
        }
        if ($senha !== $dados['confirmar_senha']) {
            return $this->errorResponse('As senhas não coincidem', 400);
        }
        
        // Valida telefone (se fornecido)
        $telefone = null;
        if (!empty($dados['telefone'])) {
            $telefone = preg_replace('/\D/', '', $dados['telefone']);
            if (strlen($telefone) < 10 || strlen($telefone) > 11) {
                return $this->errorResponse('Telefone inválido (deve ter DDD + 8 ou 9 dígitos)', 400);
            }
        }
        
        // Valida termos_aceitos (deve vir do app como 0 ou 1)
        if (!isset($dados['termos_aceitos']) || !in_array($dados['termos_aceitos'], [0, 1], true)) {
            return $this->errorResponse('É necessário aceitar os termos de uso', 400);
        }
        $termosAceitos = (int)$dados['termos_aceitos'];
        
        // ========== VALIDAÇÃO DO ENDEREÇO ==========
        if (empty($dados['endereco']) || !is_array($dados['endereco'])) {
            return $this->errorResponse('Endereço de entrega é obrigatório', 400);
        }
        
        $enderecoData = $dados['endereco'];
        $camposEndereco = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'];
        foreach ($camposEndereco as $campo) {
            if (empty($enderecoData[$campo])) {
                return $this->errorResponse("O campo de endereço '$campo' é obrigatório", 400);
            }
        }
        
        $cep = preg_replace('/\D/', '', $enderecoData['cep']);
        if (strlen($cep) !== 8) {
            return $this->errorResponse('CEP inválido (deve conter 8 dígitos)', 400);
        }
        
        $uf = strtoupper(trim($enderecoData['uf']));
        if (strlen($uf) !== 2) {
            return $this->errorResponse('UF inválida', 400);
        }
        
        // ========== CRIAÇÃO DOS REGISTROS (TRANSAÇÃO) ==========
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Cria o usuário
            $usuario = new Usuario();
            $usuario->nome = trim($dados['nome']);
            $usuario->email = $email;
            $usuario->telefone = $telefone;
            $usuario->setPassword($senha);
            $usuario->generateAuthKey();
            $usuario->status = Usuario::STATUS_ATIVO;
            $usuario->termos_aceitos = $termosAceitos;           // ← 0 ou 1 vindo do app
            $usuario->termos_aceitos_em = date('Y-m-d H:i:s');   // ← data/hora do servidor
            
            if (!empty($dados['cpf'])) {
                $usuario->cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
            }
            
            if (!$usuario->save()) {
                throw new \Exception('Erro ao salvar usuário: ' . json_encode($usuario->errors));
            }
            
            // Cria o endereço associado
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
            
            // Se o endereço não tem coordenadas, tenta geocodificar
            if ($endereco->latitude === null || $endereco->longitude === null) {
                $this->enriquecerCoordenadas($endereco);
                $endereco->save(false);
            }
            
            $transaction->commit();
            
            $this->updateLoginMetadata($usuario, 'email');
            
            return $this->successResponse(
                $this->formatUserWithTokens($usuario),
                'Cadastro realizado com sucesso',
                201
            );
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Erro no cadastro: ' . $e->getMessage(), __METHOD__);
            return $this->errorResponse('Erro ao processar cadastro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/app/auth/social
     * Login/Cadastro via redes sociais (Google, Facebook, Apple)
     */
    public function actionSocial()
    {
        $request = Yii::$app->request;
        $provider = $request->post('provider');
        $token = $request->post('token');
        $additionalData = $request->post('additionalData', []);
        
        if (empty($provider) || empty($token)) {
            return $this->errorResponse('Provider e token são obrigatórios', 400);
        }
        
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            return $this->errorResponse('Provider não suportado', 400);
        }
        
        try {
            $socialUser = $this->validateSocialToken($provider, $token, $additionalData);
            $usuario = $this->findOrCreateSocialUser($provider, $socialUser);
            $this->updateLoginMetadata($usuario, $provider);
            
            return $this->successResponse(
                $this->formatUserWithTokens($usuario),
                'Login social realizado com sucesso'
            );
        } catch (\Exception $e) {
            Yii::error("Social login error ({$provider}): " . $e->getMessage(), __METHOD__);
            return $this->errorResponse('Token inválido ou expirado', 401);
        }
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
            'avatar' => $usuario->avatar,
            'ultimo_login_em' => $usuario->ultimo_login_em,
            'criado_em' => $usuario->criado_em,
        ]);
    }

    // ==================== MÉTODOS AUXILIARES ====================

    private function updateLoginMetadata(Usuario $usuario, $provider)
    {
        $usuario->ultimo_login_em = date('Y-m-d H:i:s');
        $usuario->ultimo_login_ip = Yii::$app->request->userIP;
        $usuario->login_count = ($usuario->login_count ?? 0) + 1;
        $usuario->ultimo_login_provider = $provider;
        $usuario->save(false);
    }

    private function formatUserWithTokens(Usuario $usuario)
    {
        return [
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'telefone' => $usuario->telefone,
            'avatar' => $usuario->avatar,
            'access_token' => $usuario->generateAccessToken(),
            'refresh_token' => $usuario->generateRefreshToken(),
            'expires_in' => 7200,
            'token_type' => 'Bearer',
        ];
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
            $usuario->termos_aceitos = 1;                         // ← assume que aceitou ao usar rede social
            $usuario->termos_aceitos_em = date('Y-m-d H:i:s');   // ← data/hora do servidor
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
            // Silencioso - não interrompe o cadastro
        }
    }
}