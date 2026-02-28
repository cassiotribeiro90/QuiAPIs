<?php
namespace app\models\common;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Model para a tabela usuarios
 * 
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha_hash
 * @property string $auth_key
 * @property string $token_acesso
 * @property string $tipo (lojista, app, gestor)
 * @property int $status (10=ativo, 0=inativo)
 * @property string $created_at
 * @property string $updated_at
 */
class Usuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 10;
    const STATUS_INATIVO = 0;
    
    const TIPO_LOJISTA = 'lojista';
    const TIPO_APP = 'app';
    const TIPO_GESTOR = 'gestor';

    public static function tableName()
    {
        return 'usuarios';
    }

    public function rules()
    {
        return [
            [['nome', 'email', 'senha_hash'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['nome', 'email'], 'string', 'max' => 255],
            [['tipo'], 'string', 'max' => 20],
            [['tipo'], 'default', 'value' => self::TIPO_APP],
            [['tipo'], 'in', 'range' => [self::TIPO_LOJISTA, self::TIPO_APP, self::TIPO_GESTOR]],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ATIVO],
            [['auth_key'], 'string', 'max' => 32],
            [['token_acesso'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'tipo' => 'Tipo de Usuário',
            'status' => 'Status',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    /**
     * Gera hash da senha
     */
    public function setSenha($senha)
    {
        $this->senha_hash = Yii::$app->security->generatePasswordHash($senha);
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Valida senha
     */
    public function validarSenha($senha)
    {
        return Yii::$app->security->validatePassword($senha, $this->senha_hash);
    }

    // IdentityInterface
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['token_acesso' => $token, 'status' => self::STATUS_ATIVO]);
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