<?php
// models/api/gestor/StoreUsuario.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "store_usuario".
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string|null $telefone
 * @property string|null $cpf_cnpj
 * @property string $senha_hash
 * @property string $auth_key
 * @property string|null $access_token
 * @property string|null $access_token_expira_em
 * @property string $funcao  // proprietario, gerente, vendedor
 * @property int $status  // 1 ativo, 0 inativo, 2 bloqueado
 * @property string|null $ultimo_login_em
 * @property string|null $ultimo_login_ip
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 */
class StoreUsuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;
    const STATUS_BLOQUEADO = 2;

    const FUNCAO_PROPRIETARIO = 'proprietario';
    const FUNCAO_GERENTE = 'gerente';
    const FUNCAO_VENDEDOR = 'vendedor';

    public static function tableName()
    {
        return '{{%store_usuario}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'criado_em',
                'updatedAtAttribute' => 'atualizado_em',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['nome', 'email', 'senha_hash', 'auth_key'], 'required'],
            [['email'], 'unique'],
            [['cpf_cnpj'], 'unique'],
            [['status'], 'integer'],
            [['ultimo_login_em', 'access_token_expira_em', 'criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
            [['funcao'], 'string'],
            [['nome', 'email'], 'string', 'max' => 100],
            [['telefone'], 'string', 'max' => 20],
            [['cpf_cnpj'], 'string', 'max' => 20],
            [['senha_hash', 'access_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['ultimo_login_ip'], 'string', 'max' => 45],
            ['funcao', 'in', 'range' => [self::FUNCAO_PROPRIETARIO, self::FUNCAO_GERENTE, self::FUNCAO_VENDEDOR]],
            [['email'], 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'telefone' => 'Telefone',
            'cpf_cnpj' => 'CPF/CNPJ',
            'senha_hash' => 'Senha',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'access_token_expira_em' => 'Access Token Expira em',
            'funcao' => 'Função',
            'status' => 'Status',
            'ultimo_login_em' => 'Último Login',
            'ultimo_login_ip' => 'Último IP',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }

    public static function find()
    {
        return parent::find()->andWhere(['deletado_em' => null]);
    }

    public function softDelete()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    public function isAtivo()
    {
        return $this->status == self::STATUS_ATIVO && $this->deletado_em === null;
    }

    // IdentityInterface
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ATIVO]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->where(['access_token' => $token])
            ->andWhere(['>', 'access_token_expira_em', date('Y-m-d H:i:s')])
            ->andWhere(['status' => self::STATUS_ATIVO])
            ->andWhere(['deletado_em' => null])
            ->one();
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

    // Métodos de autenticação
    public static function findByEmail($email)
    {
        return static::find()
            ->where(['email' => $email])
            ->andWhere(['deletado_em' => null])
            ->one();
    }

    public function setPassword($password)
    {
        $this->senha_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->senha_hash);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken($duracao = 7200)
    {
        $this->access_token = Yii::$app->security->generateRandomString(64);
        $this->access_token_expira_em = date('Y-m-d H:i:s', time() + $duracao);
        $this->save(false);
        return $this->access_token;
    }

    public function generateRefreshToken($duracao = 2592000)
    {
        $this->refresh_token = Yii::$app->security->generateRandomString(64);
        $this->refresh_token_expira_em = date('Y-m-d H:i:s', time() + $duracao);
        $this->save(false);
        return $this->refresh_token;
    }

    public static function findByRefreshToken($refreshToken)
    {
        return static::find()
            ->where(['refresh_token' => $refreshToken])
            ->andWhere(['>', 'refresh_token_expira_em', date('Y-m-d H:i:s')])
            ->andWhere(['status' => self::STATUS_ATIVO])
            ->andWhere(['deletado_em' => null])
            ->one();
    }

    public function invalidateTokens()
    {
        $this->access_token = null;
        $this->access_token_expira_em = null;
        $this->refresh_token = null;
        $this->refresh_token_expira_em = null;
        return $this->save(false);
    }

    // Relacionamentos
    public function getLojas()
    {
        return $this->hasMany(Loja::class, ['id' => 'loja_id'])
            ->viaTable('store_usuario_loja', ['usuario_id' => 'id']);
    }

    public function getStoreUsuarioLojas()
    {
        return $this->hasMany(StoreUsuarioLoja::class, ['usuario_id' => 'id']);
    }
}