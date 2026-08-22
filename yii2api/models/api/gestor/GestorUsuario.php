<?php
// models/api/gestor/GestorUsuario.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha_hash
 * @property string $auth_key
 * @property int $status
 * @property string|null $access_token
 * @property string|null $access_token_expira_em
 * @property string|null $refresh_token
 * @property string|null $refresh_token_expira_em
 * @property string|null $device_id
 * @property string|null $device_token
 * @property string|null $ultimo_login_ip
 * @property string|null $ultimo_login_em
 * @property string|null $deletado_em
 * @property string $criado_em
 * @property string $atualizado_em
 */
class GestorUsuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;
    const STATUS_BLOQUEADO = 2;
    
    // Cenários para validação
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    // const TOKEN_AUTH_SECS = 120; // 2 min
    const TOKEN_AUTH_SECS = 86400; // 12 hours
    const TOKEN_REFRESH_SECS = 5184000; // 2 months

    public static function tableName()
    {
        return '{{%gestor_usuario}}';
    }

    // ========== REGRAS DE VALIDAÇÃO ==========
    
    public function rules()
    {
        return [
            // Campos obrigatórios
            [['nome', 'email', 'senha_hash', 'auth_key'], 'required'],
            
            // Email
            [['email'], 'email'],
            [['email'], 'unique', 'message' => 'Este e-mail já está em uso.'],
            
            // CPF (opcional)
            [['cpf'], 'unique', 'skipOnEmpty' => true],
            
            // Status
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ATIVO],
            
            // Nível
            [['nivel'], 'string', 'max' => 50],
            [['nivel'], 'default', 'value' => 'comercial'],
            
            // Strings
            [['nome', 'email', 'cpf', 'telefone'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['access_token', 'refresh_token'], 'string', 'max' => 255],
            [['ultimo_login_ip'], 'string', 'max' => 45],
            
            // 🔥 DEVICE ID E DEVICE TOKEN (FCM)
            [['device_id', 'device_token'], 'string', 'max' => 255],
            [['device_id', 'device_token'], 'safe'],
            
            // Datas
            [['ultimo_login_em', 'criado_em', 'atualizado_em', 'deletado_em', 
              'access_token_expira_em', 'refresh_token_expira_em'], 'safe'],
        ];
    }

    // ========== LABELS ==========
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'cpf' => 'CPF',
            'telefone' => 'Telefone',
            'nivel' => 'Nível',
            'status' => 'Status',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'access_token_expira_em' => 'Access Token Expira Em',
            'refresh_token' => 'Refresh Token',
            'refresh_token_expira_em' => 'Refresh Token Expira Em',
            'device_id' => 'ID do Dispositivo',
            'device_token' => 'Token do Dispositivo (FCM)',
            'ultimo_login_ip' => 'Último IP',
            'ultimo_login_em' => 'Último Login',
            'deletado_em' => 'Deletado em',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
        ];
    }

    // ========== IMPLEMENTAÇÃO DO IDENTITY INTERFACE ==========
    
    /**
     * Busca usuário pelo ID
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ATIVO]);
    }
    
    /**
     * Busca usuário pelo token de acesso
     * Este método é chamado automaticamente pelo Yii
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->where(['access_token' => $token])
            ->andWhere(['>', 'access_token_expira_em', date('Y-m-d H:i:s')])
            ->andWhere(['status' => self::STATUS_ATIVO])
            ->andWhere(['deletado_em' => null])
            ->one();
    }
    
    /**
     * Busca usuário pelo refresh token
     */
    public static function findByRefreshToken($refreshToken)
    {
        return static::find()
            ->where(['refresh_token' => $refreshToken])
            ->andWhere(['>', 'refresh_token_expira_em', date('Y-m-d H:i:s')])
            ->andWhere(['status' => self::STATUS_ATIVO])
            ->andWhere(['deletado_em' => null])
            ->one();
    }
    
    /**
     * Busca usuário pelo email
     */
    public static function findByEmail($email)
    {
        return static::find()
            ->where(['email' => $email])
            ->andWhere(['deletado_em' => null])
            ->one();
    }
    
    /**
     * Retorna ID do usuário
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Retorna auth key (usado para cookie-based login)
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    
    /**
     * Valida auth key
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
    
    // ========== MÉTODOS DE AUTENTICAÇÃO ==========
    
    /**
     * Gera auth key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
    /**
     * Gera hash da senha
     */
    public function setPassword($password)
    {
        $this->senha_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * Valida senha
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->senha_hash);
    }
    
    // ========== MÉTODOS DE TOKEN ==========
    
    /**
     * Gera novo access token
     * @param int $duracaoSegundos Tempo de expiração em segundos
     * @return string
     */
    public function generateAccessToken($duracaoSegundos = self::TOKEN_AUTH_SECS)
    {
        // Gera token único
        $this->access_token = Yii::$app->security->generateRandomString(64);
        $this->access_token_expira_em = date('Y-m-d H:i:s', time() + $duracaoSegundos);
        
        $this->save(false);
        
        return $this->access_token;
    }
    
    /**
     * Gera novo refresh token
     * @param int $duracaoSegundos Tempo de expiração em segundos
     * @return string
     */
    public function generateRefreshToken($duracaoSegundos = self::TOKEN_REFRESH_SECS) {
        $this->refresh_token = Yii::$app->security->generateRandomString(64);
        $this->refresh_token_expira_em = date('Y-m-d H:i:s', time() + $duracaoSegundos);
        
        $this->save(false);
        
        return $this->refresh_token;
    }
    
    /**
     * Invalida todos os tokens do usuário (logout)
     */
    public function invalidateTokens()
    {
        $this->access_token = null;
        $this->access_token_expira_em = null;
        $this->refresh_token = null;
        $this->refresh_token_expira_em = null;
        
        return $this->save(false);
    }
    
    /**
     * Verifica se access token é válido
     */
    public function isAccessTokenValid()
    {
        return !empty($this->access_token) && 
               strtotime($this->access_token_expira_em) > time();
    }
    
    /**
     * Verifica se refresh token é válido
     */
    public function isRefreshTokenValid()
    {
        return !empty($this->refresh_token) && 
               strtotime($this->refresh_token_expira_em) > time();
    }
    
    // ========== MÉTODOS AUXILIARES ==========
    
    /**
     * Verifica se usuário está ativo
     */
    public function isAtivo()
    {
        return $this->status == self::STATUS_ATIVO && $this->deletado_em === null;
    }

    // ==================== MÉTODOS PARA DEVICE TOKEN ====================

    /**
     * Atualiza o device token do gestor
     * 
     * @param string|null $token
     * @param string|null $deviceId
     * @return bool
     */
    public function updateDevice($token = null, $deviceId = null)
    {
        if ($token !== null) {
            $this->device_token = $token;
        }
        if ($deviceId !== null) {
            $this->device_id = $deviceId;
        }
        return $this->save(false);
    }

    /**
     * Remove o device token (logout)
     * 
     * @return bool
     */
    public function clearDevice()
    {
        $this->device_token = null;
        $this->device_id = null;
        return $this->save(false);
    }

    /**
     * Verifica se o gestor possui um dispositivo registrado
     * 
     * @return bool
     */
    public function hasDevice()
    {
        return !empty($this->device_token) && !empty($this->device_id);
    }

    // ==================== SOFT DELETE ====================
    
    /**
     * Sobrescreve o método find() para filtrar registros não deletados
     */
    public static function find()
    {
        return parent::find()->andWhere(['deletado_em' => null]);
    }
    
    /**
     * Soft delete
     */
    public function softDelete()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        return $this->save(false);
    }
}