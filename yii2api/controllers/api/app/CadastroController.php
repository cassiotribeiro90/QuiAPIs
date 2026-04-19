<?php

namespace app\controllers\api\app;

use Yii;
use app\controllers\api\app\AppControllerBase;
use app\models\api\app\Usuario;
use app\models\api\app\Endereco;
use GuzzleHttp\Client;
use yii\db\Exception;

/**
 * Controller responsável pelo cadastro de usuários.
 * 
 * Endpoints públicos:
 * - POST /api/app/cadastro/validar-etapa1    Validação dos dados pessoais
 * - GET  /api/app/cadastro/buscar-cep        Consulta de endereço por CEP (ViaCEP)
 * - POST /api/app/cadastro/cadastrar         Criação do usuário e endereço
 */
class CadastroController extends AppControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     * Remove autenticação das ações públicas de cadastro.
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'validar-etapa1',
                'buscar-cep',
                'cadastrar',
            ];
        }
        
        return $behaviors;
    }

    /**
     * GET /api/app/cadastro/buscar-cep?cep=30130000
     * 
     * Consulta o ViaCEP e retorna os dados do endereço.
     * 
     * @param string $cep CEP com 8 dígitos (somente números)
     * @return array
     */
    public function actionBuscarCep($cep = null)
    {
        if ($cep === null) {
            $cep = Yii::$app->request->get('cep');
        }
        
        if (empty($cep)) {
            return $this->errorResponse('CEP não informado', 400);
        }
        
        // Remove caracteres não numéricos
        $cep = preg_replace('/\D/', '', $cep);
        
        if (strlen($cep) !== 8) {
            return $this->errorResponse('CEP inválido (deve conter 8 dígitos)', 400);
        }
        
        try {
            $client = new Client(['timeout' => 5]);
            $response = $client->get("https://viacep.com.br/ws/{$cep}/json/");
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['erro']) && $data['erro'] === true) {
                return $this->errorResponse('CEP não encontrado', 404);
            }
            
            return $this->successResponse([
                'cep' => $data['cep'] ?? $cep,
                'logradouro' => $data['logradouro'] ?? '',
                'complemento' => $data['complemento'] ?? '',
                'bairro' => $data['bairro'] ?? '',
                'cidade' => $data['localidade'] ?? '',
                'uf' => $data['uf'] ?? '',
            ], 'CEP encontrado com sucesso');
            
        } catch (\Exception $e) {
            Yii::error("Erro ao consultar ViaCEP: " . $e->getMessage(), __METHOD__);
            return $this->errorResponse('Serviço de consulta de CEP indisponível', 503);
        }
    }

    /**
     * POST /api/app/cadastro/validar-etapa1
     * 
     * Valida os dados pessoais (sem persistência).
     * Espera JSON: nome, email, telefone, senha, confirmar_senha.
     */
    public function actionValidarEtapa1()
    {
        $request = Yii::$app->request;
        $dados = $request->post();

        // Validação de presença
        $camposObrigatorios = ['nome', 'email', 'telefone', 'senha', 'confirmar_senha'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                return $this->errorResponse("O campo '$campo' é obrigatório", 400);
            }
        }

        // Validação de email
        $email = trim($dados['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Email inválido', 400);
        }

        // Verifica se o email já existe
        if (Usuario::find()->where(['email' => $email])->exists()) {
            return $this->errorResponse('Este email já está cadastrado', 409);
        }

        // Validação de senha
        $senha = $dados['senha'];
        if (strlen($senha) < 6) {
            return $this->errorResponse('A senha deve ter pelo menos 6 caracteres', 400);
        }
        if ($senha !== $dados['confirmar_senha']) {
            return $this->errorResponse('As senhas não coincidem', 400);
        }

        // Validação de telefone (apenas dígitos, 10 ou 11 caracteres)
        $telefone = preg_replace('/\D/', '', $dados['telefone']);
        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            return $this->errorResponse('Telefone inválido (deve ter DDD + 8 ou 9 dígitos)', 400);
        }

        return $this->successResponse(null, 'Dados pessoais válidos');
    }

    /**
     * POST /api/app/cadastro/cadastrar
     * 
     * Valida todos os dados (pessoais + endereço) e cria os registros.
     * Espera JSON com campos pessoais e de endereço.
     */
    public function actionCadastrar()
    {
        $request = Yii::$app->request;
        $dados = $request->post();

        // ========== VALIDAÇÃO DOS DADOS PESSOAIS ==========
        $camposPessoais = ['nome', 'email', 'telefone', 'senha', 'confirmar_senha'];
        foreach ($camposPessoais as $campo) {
            if (empty($dados[$campo])) {
                return $this->errorResponse("O campo '$campo' é obrigatório", 400);
            }
        }

        $email = trim($dados['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Email inválido', 400);
        }

        if (Usuario::find()->where(['email' => $email])->exists()) {
            return $this->errorResponse('Este email já está cadastrado', 409);
        }

        $senha = $dados['senha'];
        if (strlen($senha) < 6) {
            return $this->errorResponse('A senha deve ter pelo menos 6 caracteres', 400);
        }
        if ($senha !== $dados['confirmar_senha']) {
            return $this->errorResponse('As senhas não coincidem', 400);
        }

        $telefone = preg_replace('/\D/', '', $dados['telefone']);
        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            return $this->errorResponse('Telefone inválido', 400);
        }

        // ========== VALIDAÇÃO DO ENDEREÇO ==========
        $camposEndereco = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'];
        foreach ($camposEndereco as $campo) {
            if (empty($dados[$campo])) {
                return $this->errorResponse("O campo de endereço '$campo' é obrigatório", 400);
            }
        }

        $cep = preg_replace('/\D/', '', $dados['cep']);
        if (strlen($cep) !== 8) {
            return $this->errorResponse('CEP inválido (deve conter 8 dígitos)', 400);
        }

        $uf = strtoupper(trim($dados['uf']));
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
            $usuario->termos_aceitos = true;
            $usuario->termos_aceitos_em = date('Y-m-d H:i:s');

            if (!$usuario->save()) {
                throw new \Exception('Erro ao salvar usuário: ' . json_encode($usuario->errors));
            }

            // Cria o endereço associado
            $endereco = new Endereco();
            $endereco->usuario_id = $usuario->id;
            $endereco->cep = $cep;
            $endereco->logradouro = trim($dados['logradouro']);
            $endereco->numero = trim($dados['numero']);
            $endereco->complemento = isset($dados['complemento']) ? trim($dados['complemento']) : null;
            $endereco->bairro = trim($dados['bairro']);
            $endereco->cidade = trim($dados['cidade']);
            $endereco->uf = $uf;
            $endereco->apelido = isset($dados['apelido']) ? trim($dados['apelido']) : 'Principal';
            $endereco->destinatario = isset($dados['destinatario']) ? trim($dados['destinatario']) : $usuario->nome;
            $endereco->tipo = 'entrega';
            $endereco->padrao = 1;

            if (!$endereco->save()) {
                throw new \Exception('Erro ao salvar endereço: ' . json_encode($endereco->errors));
            }

            $transaction->commit();

            // Gera tokens de acesso
            $accessToken = $usuario->generateAccessToken();
            $refreshToken = $usuario->generateRefreshToken();

            return $this->successResponse([
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'telefone' => $usuario->telefone,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 7200,
                'token_type' => 'Bearer',
            ], 'Cadastro realizado com sucesso', 201);

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Erro no cadastro: ' . $e->getMessage(), __METHOD__);
            return $this->errorResponse('Erro ao processar cadastro: ' . $e->getMessage(), 500);
        }
    }
}