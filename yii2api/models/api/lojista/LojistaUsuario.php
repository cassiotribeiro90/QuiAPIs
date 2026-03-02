<?php
namespace app\models\common;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Model para a tabela lojista_usuarios
 * 
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $avatar
 * @property string $senha_hash
 * @property string $auth_key
 * @property string $token_acesso
 * 
 * ========== CAMPOS SOCIAIS ==========
 * @property string $google_id
 * @property string $facebook_id
 * @property bool $email_verified
 * 
 * ========== CAMPOS DA LOJA ==========
 * @property int $loja_id
 * @property string $cargo
 * @property string $permissoes
 * 
 * ========== METADADOS ==========
 * @property int $status
 * @property string $ultimo_login_at
 * @property string $ultimo_login_ip
 * @property string $created_at
 * @property string $updated_at
 */
class LojistaUsuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 10;
    const STATUS_INATIVO = 0;
    const STATUS_BLOQUEADO = 5;
    
    const CARGO_PROPIETARIO = 'proprietario';
    const CARGO_GERENTE = 'gerente';
    const CARGO_COZINHA = 'cozinha';
    const CARGO_ENTREGADOR = 'entregador';
    const CARGO_VISUALIZADOR = 'visualizador';

    public static function tableName()
    {
        return 'lojista_usuarios';
    }

    public function rules()
    {
        return [
            // ========== CAMPOS OBRIGATÓRIOS ==========
            [['nome', 'email'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            
            // ========== STRINGS ==========
            [['nome', 'email'], 'string', 'max' => 255],
            [['avatar'], 'string', 'max' => 500],
            [['auth_key'], 'string', 'max' => 32],
            [['token_acesso'], 'string', 'max' => 255],
            [['ultimo_login_ip'], 'string', 'max' => 45],
            
            // ========== CAMPOS SOCIAIS ==========
            [['google_id', 'facebook_id'], 'string', 'max' => 255],
            [['google_id'], 'unique'],
            [['facebook_id'], 'unique'],
            [['email_verified'], 'boolean'],
            
            // ========== CAMPOS DA LOJA ==========
            [['loja_id'], 'integer'],
            [['cargo'], 'string', 'max' => 50],
            [['cargo'], 'in', 'range' => [
                self::CARGO_PROPIETARIO,
                self::CARGO_GERENTE,
                self::CARGO_COZINHA,
                self::CARGO_ENTREGADOR,
                self::CARGO_VISUALIZADOR
            ]],
            [['permissoes'], 'safe'], // JSON field
            
            // ========== STATUS ==========
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ATIVO],
            [['status'], 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_INATIVO, self::STATUS_BLOQUEADO]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'cargo' => 'Cargo',
            'loja_id' => 'Loja',
            'status' => 'Status',
            'email_verified' => 'E-mail Verificado',
            'ultimo_login_at' => 'Último Login',
            'created_at' => 'Criado em',
            'google_id' => 'ID Google',
            'facebook_id' => 'ID Facebook',
        ];
    }

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
     * Gera novo token de acesso
     */
    public function gerarTokenAcesso()
    {
        $this->token_acesso = Yii::$app->security->generateRandomString();
        return $this->token_acesso;
    }

    /**
     * Invalida token atual
     */
    public function invalidarToken()
    {
        $this->token_acesso = null;
    }

    /**
     * Atualiza informações de login
     */
    public function registrarLogin()
    {
        $this->ultimo_login_at = date('Y-m-d H:i:s');
        $this->ultimo_login_ip = Yii::$app->request->userIP;
        $this->gerarTokenAcesso();
    }

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
     * Verifica se é proprietário da loja
     */
    public function isProprietario()
    {
        return $this->cargo === self::CARGO_PROPIETARIO;
    }

    /**
     * Verifica se tem permissão específica
     */
    public function hasPermissao($permissao)
    {
        if ($this->isProprietario()) {
            return true;
        }
        
        $permissoes = json_decode($this->permissoes, true) ?? [];
        return in_array($permissao, $permissoes);
    }

    /**
     * Busca ou cria usuário por email (para login social)
     */
    public static function findOrCreateByEmail($email, $nome, $provider, $providerId, $avatar = null, $cargo = self::CARGO_VISUALIZADOR)
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
            $usuario->cargo = $cargo;
            $usuario->email_verified = true;
            $usuario->auth_key = Yii::$app->security->generateRandomString();
            
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

    // ========== IdentityInterface ==========

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'token_acesso' => $token,
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