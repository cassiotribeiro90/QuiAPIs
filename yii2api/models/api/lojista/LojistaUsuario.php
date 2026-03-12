<?php

namespace app\models\api\lojista;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha_hash
 * @property string $cpf_cnpj
 * @property int $status
 * @property string $role
 * @property string $auth_key
 * @property string $token_acesso
 * @property string|null $ultimo_login_ip
 * @property string|null $ultimo_login_em
 * @property string $criado_em
 * @property string $atualizado_em
 */
class LojistaUsuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;
    const STATUS_BLOQUEADO = 2;

    const ROLE_ADMIN = 'admin';
    const ROLE_GERENTE = 'gerente';
    const ROLE_VENDEDOR = 'vendedor';

    public static function tableName()
    {
        return 'lojista_usuarios';
    }

    public function rules()
    {
        return [
            [['nome', 'email', 'senha_hash', 'cpf_cnpj'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['cpf_cnpj'], 'unique'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ATIVO],
            [['role'], 'default', 'value' => self::ROLE_VENDEDOR],
            [['role'], 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_GERENTE, self::ROLE_VENDEDOR]],
            [['nome', 'email', 'telefone', 'cpf_cnpj'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['token_acesso'], 'string', 'max' => 255],
            [['ultimo_login_ip'], 'string', 'max' => 45],
            [['ultimo_login_em', 'criado_em', 'atualizado_em'], 'safe'],
        ];
    }

    // ========== MÉTODOS DE SENHA ==========
    
    public function setSenha($senha)
    {
        $this->senha_hash = Yii::$app->security->generatePasswordHash($senha);
    }

    public function validarSenha($senha)
    {
        return Yii::$app->security->validatePassword($senha, $this->senha_hash);
    }

    // ========== MÉTODOS DE TOKEN ==========
    
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateTokenAcesso()
    {
        $this->token_acesso = Yii::$app->security->generateRandomString(32);
    }

    // ========== MÉTODOS DO IDENTITY INTERFACE ==========
    
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ATIVO]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token_acesso' => $token, 'status' => self::STATUS_ATIVO]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ATIVO]);
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