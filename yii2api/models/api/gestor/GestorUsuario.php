<?php
// models/api/gestor/GestorUsuario.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class GestorUsuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;
    const STATUS_BLOQUEADO = 2;
    
    // Cenários para validação
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    public static function tableName()
    {
        return '{{%gestor_usuario}}';
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
    //public function generateAccessToken($duracaoSegundos = 7200) // 2 horas padrão
    public function generateAccessToken($duracaoSegundos = 900) // 15 minutos para testes
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
    //public function generateRefreshToken($duracaoSegundos = 2592000) // 30 dias padrão
    public function generateRefreshToken($duracaoSegundos = 3600) // 1h para testes
    {
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
}