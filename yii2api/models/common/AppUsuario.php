<?php
namespace app\models\common;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Model para a tabela app_usuarios
 * 
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $cpf
 * @property string $data_nascimento
 * @property string $telefone
 * @property bool $telefone_verified
 * @property string $whatsapp
 * @property string $senha_hash
 * @property string $auth_key
 * @property string $access_token
 * @property string $access_token_expires_at
 * @property string $password_reset_token
 * @property string $password_reset_expires_at
 * @property string $tipo
 * @property string $status
 * @property string $ultimo_login_em
 * @property string $ultimo_login_ip
 * @property int $login_count
 * @property string $primeira_compra_at
 * @property string $ultima_compra_at
 * @property int $total_compras
 * @property float $total_gasto
 * @property int $pontos
 * @property int $nivel
 * @property int $indicado_por
 * @property string $codigo_indicacao
 * @property int $indicacoes_count
 * @property bool $pref_notificacoes_email
 * @property bool $pref_notificacoes_push
 * @property bool $pref_notificacoes_sms
 * @property string $pref_tema
 * @property bool $email_verified
 * @property bool $termos_aceitos
 * @property string $termos_aceitos_at
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string $deletado_em
 * 
 * ========== NOVOS CAMPOS SOCIAIS ==========
 * @property string $google_id
 * @property string $facebook_id
 * @property string $avatar
 */
class AppUsuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    const STATUS_BLOQUEADO = 'bloqueado';
    const STATUS_PENDENTE = 'pendente';
    
    const TIPO_CLIENTE = 'cliente';
    const TIPO_ADMIN = 'admin';

    public static function tableName()
    {
        return 'app_usuarios';
    }

    public function rules()
    {
        return [
            // ========== CAMPOS EXISTENTES ==========
            [['nome', 'email'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['cpf'], 'unique'],
            [['codigo_indicacao'], 'unique'],
            
            [['nome', 'email'], 'string', 'max' => 255],
            [['cpf'], 'string', 'max' => 11],
            [['telefone', 'whatsapp'], 'string', 'max' => 20],
            [['data_nascimento'], 'safe'],
            
            [['telefone_verified', 'email_verified', 'termos_aceitos'], 'boolean'],
            [['pref_notificacoes_email', 'pref_notificacoes_push', 'pref_notificacoes_sms'], 'boolean'],
            
            [['tipo'], 'in', 'range' => [self::TIPO_CLIENTE, self::TIPO_ADMIN]],
            [['status'], 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_INATIVO, self::STATUS_BLOQUEADO, self::STATUS_PENDENTE]],
            [['pref_tema'], 'in', 'range' => ['light', 'dark', 'auto']],
            
            [['login_count', 'total_compras', 'pontos', 'nivel', 'indicacoes_count'], 'integer'],
            [['total_gasto'], 'number'],
            
            [['indicado_por'], 'integer'],
            [['indicado_por'], 'exist', 'targetClass' => self::class, 'targetAttribute' => 'id'],
            
            [['auth_key'], 'string', 'max' => 32],
            [['access_token', 'password_reset_token', 'codigo_indicacao'], 'string', 'max' => 255],
            [['ultimo_login_ip'], 'string', 'max' => 45],
            
            // ========== NOVOS CAMPOS SOCIAIS ==========
            [['google_id', 'facebook_id'], 'string', 'max' => 255],
            [['avatar'], 'string', 'max' => 500],
            [['google_id'], 'unique'],
            [['facebook_id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'cpf' => 'CPF',
            'telefone' => 'Telefone',
            'status' => 'Status',
            'tipo' => 'Tipo',
            'pontos' => 'Pontos',
            'nivel' => 'Nível',
            'criado_em' => 'Cadastro',
            'ultimo_login_em' => 'Último Login',
            'google_id' => 'ID Google',
            'facebook_id' => 'ID Facebook',
            'avatar' => 'Foto',
        ];
    }

    // ========== MÉTODOS DE AUTENTICAÇÃO ==========

    /**
     * Gera hash da senha e auth_key
     */
    public function setSenha($senha)
    {
        $security = Yii::$app->security;
        $this->senha_hash = $security->generatePasswordHash($senha);
        $this->auth_key = $security->generateRandomString();
    }

    /**
     * Valida senha
     */
    public function validarSenha($senha)
    {
        if (empty($this->senha_hash)) {
            return false;
        }
        return Yii::$app->security->validatePassword($senha, $this->senha_hash);
    }

    /**
     * Gera novo token de acesso com expiração
     */
    public function gerarTokenAcesso($dias = 30)
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        $this->access_token_expires_at = date('Y-m-d H:i:s', strtotime("+{$dias} days"));
        return $this->access_token;
    }

    /**
     * Invalida token atual
     */
    public function invalidarToken()
    {
        $this->access_token = null;
        $this->access_token_expires_at = null;
    }

    /**
     * Verifica se token é válido
     */
    public function tokenValido()
    {
        if (empty($this->access_token) || empty($this->access_token_expires_at)) {
            return false;
        }
        return strtotime($this->access_token_expires_at) > time();
    }

    /**
     * Atualiza informações de login
     */
    public function registrarLogin()
    {
        $this->ultimo_login_em = date('Y-m-d H:i:s');
        $this->ultimo_login_ip = Yii::$app->request->userIP;
        $this->login_count = ($this->login_count ?? 0) + 1;
        $this->gerarTokenAcesso();
    }

    // ========== MÉTODOS DE VERIFICAÇÃO ==========

    /**
     * Verifica se usuário tem senha
     */
    public function hasSenha()
    {
        return !empty($this->senha_hash);
    }

    /**
     * Verifica se usuário tem Google
     */
    public function hasGoogle()
    {
        return !empty($this->google_id);
    }

    /**
     * Verifica se usuário tem Facebook
     */
    public function hasFacebook()
    {
        return !empty($this->facebook_id);
    }

    /**
     * Verifica se é admin
     */
    public function isAdmin()
    {
        return $this->tipo === self::TIPO_ADMIN;
    }

    // ========== MÉTODOS DE NEGÓCIO ==========

    /**
     * Adiciona pontos ao usuário
     */
    public function adicionarPontos($pontos)
    {
        $this->pontos += $pontos;
        
        // Atualiza nível baseado em pontos (exemplo simples)
        if ($this->pontos >= 1000) {
            $this->nivel = 5;
        } elseif ($this->pontos >= 500) {
            $this->nivel = 4;
        } elseif ($this->pontos >= 200) {
            $this->nivel = 3;
        } elseif ($this->pontos >= 50) {
            $this->nivel = 2;
        }
        
        return $this->save();
    }

    /**
     * Registra uma compra
     */
    public function registrarCompra($valor)
    {
        $this->ultima_compra_at = date('Y-m-d H:i:s');
        
        if (empty($this->primeira_compra_at)) {
            $this->primeira_compra_at = $this->ultima_compra_at;
        }
        
        $this->total_compras++;
        $this->total_gasto += $valor;
        
        // Exemplo: 1 ponto a cada R$10
        $pontos_ganhos = floor($valor / 10);
        $this->adicionarPontos($pontos_ganhos);
        
        return $this->save();
    }

    /**
     * Aceita os termos
     */
    public function aceitarTermos()
    {
        $this->termos_aceitos = true;
        $this->termos_aceitos_at = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     * Gera código de indicação único
     */
    public static function gerarCodigoIndicacao()
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

    // ========== MÉTODOS SOCIAIS ==========

    /**
     * Busca ou cria usuário por email (para login social)
     */
    public static function findOrCreateByEmail($email, $nome, $provider, $providerId, $avatar = null)
    {
        $usuario = static::find()->where(['email' => $email])->one();
        
        if ($usuario) {
            // Usuário existe, vincula a conta social se ainda não tiver
            if ($provider === 'google' && !$usuario->google_id) {
                $usuario->google_id = $providerId;
            }
            if ($provider === 'facebook' && !$usuario->facebook_id) {
                $usuario->facebook_id = $providerId;
            }
            if ($avatar && !$usuario->avatar) {
                $usuario->avatar = $avatar;
            }
            $usuario->email_verified = true;
        } else {
            // Cria novo usuário
            $usuario = new static();
            $usuario->nome = $nome;
            $usuario->email = $email;
            $usuario->avatar = $avatar;
            $usuario->tipo = self::TIPO_CLIENTE;
            $usuario->status = self::STATUS_ATIVO;
            $usuario->email_verified = true;
            $usuario->auth_key = Yii::$app->security->generateRandomString();
            $usuario->codigo_indicacao = self::gerarCodigoIndicacao();
            
            if ($provider === 'google') {
                $usuario->google_id = $providerId;
            }
            if ($provider === 'facebook') {
                $usuario->facebook_id = $providerId;
            }
        }
        
        if ($usuario->save()) {
            return $usuario;
        }
        
        return null;
    }

    /**
     * Busca usuário por ID social
     */
    public static function findBySocialId($provider, $providerId)
    {
        $campo = $provider . '_id';
        return static::findOne([$campo => $providerId]);
    }

    // ========== IdentityInterface ==========

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token,
            'status' => self::STATUS_ATIVO
        ]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
}