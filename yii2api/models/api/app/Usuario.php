<?php
// models/api/app/Usuario.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "app_usuario".
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string|null $cpf
 * @property string|null $data_nascimento
 * @property string|null $telefone
 * @property int $telefone_verificado
 * @property string|null $whatsapp
 * @property string|null $senha_hash
 * @property string $auth_key
 * @property string|null $access_token
 * @property string|null $access_token_expira_em
 * @property string|null $reset_token
 * @property string|null $reset_token_expira_em
 * @property string|null $google_id
 * @property string|null $facebook_id
 * @property string|null $avatar
 * @property string $tipo
 * @property string $status
 * @property string|null $ultimo_login_em
 * @property string|null $ultimo_login_ip
 * @property int $login_count
 * @property string|null $primeiro_pedido_em
 * @property string|null $ultimo_pedido_em
 * @property int $total_pedidos
 * @property float $total_gasto
 * @property int $pontos
 * @property int $nivel
 * @property int|null $indicado_por
 * @property string|null $codigo_indicacao
 * @property int $indicacoes_count
 * @property int $pref_notificacoes_email
 * @property int $pref_notificacoes_push
 * @property int $pref_notificacoes_sms
 * @property string $pref_tema
 * @property int $email_verificado
 * @property int $termos_aceitos
 * @property string|null $termos_aceitos_em
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 */
class Usuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    const STATUS_BLOQUEADO = 'bloqueado';
    const STATUS_PENDENTE = 'pendente';
    
    const TIPO_CLIENTE = 'cliente';
    const TIPO_ADMIN = 'admin';

    public static function tableName()
    {
        return '{{%app_usuario}}';
    }

    // ===== REGRAS DE VALIDAÇÃO =====
    
    public function rules()
    {
        return [
            [['nome', 'email', 'auth_key'], 'required'],
            [['email'], 'unique'],
            [['cpf'], 'unique'],
            [['data_nascimento', 'ultimo_login_em', 'primeiro_pedido_em', 'ultimo_pedido_em', 
              'termos_aceitos_em', 'criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
            [['telefone_verificado', 'login_count', 'total_pedidos', 'pontos', 'nivel', 
              'indicacoes_count', 'pref_notificacoes_email', 'pref_notificacoes_push', 
              'pref_notificacoes_sms', 'email_verificado', 'termos_aceitos'], 'integer'],
            [['total_gasto'], 'number'],
            [['nome'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 150],
            [['cpf'], 'string', 'max' => 11],
            [['telefone', 'whatsapp'], 'string', 'max' => 20],
            [['senha_hash', 'access_token', 'reset_token', 'google_id', 'facebook_id', 'avatar'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['ultimo_login_ip'], 'string', 'max' => 45],
            [['codigo_indicacao'], 'string', 'max' => 20],
            [['tipo', 'status', 'pref_tema'], 'string'],
            [['email'], 'email'],
            [['cpf'], 'match', 'pattern' => '/^\d{11}$/', 'message' => 'CPF deve conter 11 dígitos'],
            ['tipo', 'in', 'range' => [self::TIPO_CLIENTE, self::TIPO_ADMIN]],
            ['status', 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_INATIVO, self::STATUS_BLOQUEADO, self::STATUS_PENDENTE]],
        ];
    }

    // ===== LABELS =====
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'cpf' => 'CPF',
            'data_nascimento' => 'Data de Nascimento',
            'telefone' => 'Telefone',
            'telefone_verificado' => 'Telefone Verificado',
            'whatsapp' => 'WhatsApp',
            'senha_hash' => 'Senha',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'access_token_expira_em' => 'Access Token Expira Em',
            'reset_token' => 'Reset Token',
            'reset_token_expira_em' => 'Reset Token Expira Em',
            'google_id' => 'Google ID',
            'facebook_id' => 'Facebook ID',
            'avatar' => 'Avatar',
            'tipo' => 'Tipo',
            'status' => 'Status',
            'ultimo_login_em' => 'Último Login',
            'ultimo_login_ip' => 'Último IP',
            'login_count' => 'Login Count',
            'primeiro_pedido_em' => 'Primeiro Pedido',
            'ultimo_pedido_em' => 'Último Pedido',
            'total_pedidos' => 'Total Pedidos',
            'total_gasto' => 'Total Gasto',
            'pontos' => 'Pontos',
            'nivel' => 'Nível',
            'indicado_por' => 'Indicado Por',
            'codigo_indicacao' => 'Código de Indicação',
            'indicacoes_count' => 'Indicações',
            'pref_notificacoes_email' => 'Notificações por E-mail',
            'pref_notificacoes_push' => 'Notificações Push',
            'pref_notificacoes_sms' => 'Notificações por SMS',
            'pref_tema' => 'Tema',
            'email_verificado' => 'E-mail Verificado',
            'termos_aceitos' => 'Termos Aceitos',
            'termos_aceitos_em' => 'Termos Aceitos Em',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }

    // ===== IDENTITY INTERFACE =====
    
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
    
    // ===== MÉTODOS DE BUSCA =====
    
    public static function findByEmail($email)
    {
        return static::find()
            ->where(['email' => $email])
            ->andWhere(['deletado_em' => null])
            ->one();
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
    
    public static function findByCodigoIndicacao($codigo)
    {
        return static::find()
            ->where(['codigo_indicacao' => $codigo])
            ->andWhere(['deletado_em' => null])
            ->one();
    }
    
    // ===== MÉTODOS DE AUTENTICAÇÃO =====
    
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
    
    public function generateAccessToken($duracao = 7200) // 2 horas
    {
        $this->access_token = Yii::$app->security->generateRandomString(64);
        $this->access_token_expira_em = date('Y-m-d H:i:s', time() + $duracao);
        $this->save(false);
        return $this->access_token;
    }
    
    public function generateRefreshToken($duracao = 2592000) // 30 dias
    {
        $this->refresh_token = Yii::$app->security->generateRandomString(64);
        $this->refresh_token_expira_em = date('Y-m-d H:i:s', time() + $duracao);
        $this->save(false);
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
    
    public function generateCodigoIndicacao()
    {
        if (empty($this->codigo_indicacao)) {
            $this->codigo_indicacao = strtoupper(substr(md5($this->id . $this->email . time()), 0, 8));
            $this->save(false);
        }
        return $this->codigo_indicacao;
    }
    
    // ===== MÉTODOS DE ESTADO =====
    
    public function isAtivo()
    {
        return $this->status == self::STATUS_ATIVO && $this->deletado_em === null;
    }
    
    public function isBloqueado()
    {
        return $this->status == self::STATUS_BLOQUEADO;
    }
    
    public function isPendente()
    {
        return $this->status == self::STATUS_PENDENTE;
    }
    
    public function isEmailVerificado()
    {
        return (bool)$this->email_verificado;
    }
    
    // ===== MÉTODOS DE RELACIONAMENTO =====
    
    public function getIndicador()
    {
        return $this->hasOne(self::class, ['id' => 'indicado_por']);
    }
    
    public function getIndicados()
    {
        return $this->hasMany(self::class, ['indicado_por' => 'id']);
    }
    
    public function getEnderecos()
    {
        return $this->hasMany(AppEndereco::class, ['usuario_id' => 'id']);
    }
    
    public function getPedidos()
    {
        return $this->hasMany(Pedido::class, ['usuario_id' => 'id']);
    }
    
    public function getAvaliacoes()
    {
        return $this->hasMany(Avaliacao::class, ['usuario_id' => 'id']);
    }
    
    // ===== MÉTODOS DE MÉTRICAS =====
    
    public function atualizarMetricas()
    {
        $this->total_pedidos = $this->getPedidos()->count();
        $this->total_gasto = $this->getPedidos()->sum('total') ?? 0;
        
        $ultimoPedido = $this->getPedidos()->orderBy(['criado_em' => SORT_DESC])->one();
        if ($ultimoPedido) {
            $this->ultimo_pedido_em = $ultimoPedido->criado_em;
        }
        
        $primeiroPedido = $this->getPedidos()->orderBy(['criado_em' => SORT_ASC])->one();
        if ($primeiroPedido) {
            $this->primeiro_pedido_em = $primeiroPedido->criado_em;
        }
        
        $this->save(false);
    }
    
    // ===== SOFT DELETE =====
    
    public static function find()
    {
        return parent::find()->andWhere(['deletado_em' => null]);
    }
    
    public function softDelete()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        return $this->save(false);
    }
    
    // ===== GETTERS EXTRAS =====
    
    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_INATIVO => 'Inativo',
            self::STATUS_BLOQUEADO => 'Bloqueado',
            self::STATUS_PENDENTE => 'Pendente',
        ];
        return $labels[$this->status] ?? 'Desconhecido';
    }
    
    public function getNivelLabel()
    {
        $niveis = [
            1 => 'Bronze',
            2 => 'Prata',
            3 => 'Ouro',
            4 => 'Platina',
            5 => 'Diamante',
        ];
        return $niveis[$this->nivel] ?? 'Bronze';
    }
}