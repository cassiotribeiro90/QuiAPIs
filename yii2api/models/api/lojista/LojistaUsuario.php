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
 * @property string|null $telefone
 * @property string|null $cpf_cnpj
 * @property int $status
 * @property string $funcao
 * @property string $auth_key
 * @property string|null $access_token
 * @property string|null $access_token_expira_em
 * @property string|null $ultimo_login_ip
 * @property string|null $ultimo_login_em
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 * @property string|null $reset_token
 * @property string|null $reset_token_expira_em
 * @property string|null $refresh_token
 * @property string|null $refresh_token_expira_em
 * @property string|null $device_id
 * @property string|null $ultimo_login_provider
 */
class LojistaUsuario extends ActiveRecord implements IdentityInterface
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

    public function rules()
    {
        return [
            [['nome', 'email', 'senha_hash', 'auth_key'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['cpf_cnpj'], 'unique'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ATIVO],
            [['funcao'], 'default', 'value' => self::FUNCAO_VENDEDOR],
            [['funcao'], 'in', 'range' => [self::FUNCAO_PROPRIETARIO, self::FUNCAO_GERENTE, self::FUNCAO_VENDEDOR]],
            [['nome', 'email', 'telefone', 'cpf_cnpj'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['access_token', 'refresh_token', 'reset_token'], 'string', 'max' => 255],
            [['ultimo_login_ip', 'device_id'], 'string', 'max' => 45],
            [['ultimo_login_em', 'criado_em', 'atualizado_em', 'deletado_em', 'reset_token_expira_em', 'refresh_token_expira_em', 'access_token_expira_em'], 'safe'],
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
            'funcao' => 'Função',
            'status' => 'Status',
            'ultimo_login_em' => 'Último Login',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
        ];
    }

    // 🔥 Soft Delete - filtra registros não deletados
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

    // ========== MÉTODOS DE SENHA ==========
    
    public function setPassword($senha)
    {
        $this->senha_hash = Yii::$app->security->generatePasswordHash($senha);
    }

    public function validatePassword($senha)
    {
        return Yii::$app->security->validatePassword($senha, $this->senha_hash);
    }

    // ========== MÉTODOS DE TOKEN ==========
    
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken($duracao = 7200)
    {
        $this->access_token = Yii::$app->security->generateRandomString(64);
        $this->access_token_expira_em = date('Y-m-d H:i:s', time() + $duracao);
        return $this->access_token;
    }

    public function generateRefreshToken($duracao = 2592000)
    {
        $this->refresh_token = Yii::$app->security->generateRandomString(64);
        $this->refresh_token_expira_em = date('Y-m-d H:i:s', time() + $duracao);
        return $this->refresh_token;
    }

    public function invalidateTokens()
    {
        $this->access_token = null;
        $this->access_token_expira_em = null;
        $this->refresh_token = null;
        $this->refresh_token_expira_em = null;
        return $this->save(false);
    }

    // ========== MÉTODOS DO IDENTITY INTERFACE ==========
    
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
            ->one();
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

    // ==================== RELACIONAMENTOS ====================

    /**
     * Relacionamento com LojistaUsuarioLoja
     */
    public function getLojistaUsuarioLojas()
    {
        return $this->hasMany(LojistaUsuarioLoja::class, ['usuario_id' => 'id']);
    }

    /**
     * Relacionamento com Lojas via tabela de ligação
     */
    public function getLojas()
    {
        return $this->hasMany(\app\models\api\app\Loja::class, ['id' => 'loja_id'])
            ->via('lojistaUsuarioLojas')
            ->where(['loja.status' => 'ativo']); // ✅ 'ativo' em vez de 1
    }

    /**
     * Retorna os IDs das lojas associadas
     */
    public function getLojaIds()
    {
        return $this->getLojas()->select('id')->column();
    }

    /**
     * Verifica se o lojista tem acesso a uma loja
     */
    public function temAcessoLoja($lojaId)
    {
        return LojistaUsuarioLoja::find()
            ->where([
                'usuario_id' => $this->id,
                'loja_id' => $lojaId,
                'status' => LojistaUsuarioLoja::STATUS_ATIVO,
            ])
            ->exists();
    }

    /**
     * Associa lojas ao lojista
     */
    public function assignLojas(array $lojaIds, $funcao = 'vendedor')
    {
        LojistaUsuarioLoja::deleteAll(['usuario_id' => $this->id]);
        
        foreach ($lojaIds as $lojaId) {
            $model = new LojistaUsuarioLoja();
            $model->usuario_id = $this->id;
            $model->loja_id = $lojaId;
            $model->funcao = $funcao;
            $model->status = LojistaUsuarioLoja::STATUS_ATIVO;
            $model->save();
        }
    }
}