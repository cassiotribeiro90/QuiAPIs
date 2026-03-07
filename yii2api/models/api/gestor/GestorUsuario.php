<?php
// Arquivo: models/api/gestor/GestorUsuario.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class GestorUsuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;
    const STATUS_BLOQUEADO = 2;
    
    const TIPO_COMERCIAL = 'comercial';
    const TIPO_ADMIN = 'admin';
    const TIPO_SUPORTE = 'suporte';

    public static function tableName()
    {
        return 'gestor_usuarios';
    }

    public function rules()
    {
        return [
            // ========== OBRIGATÓRIOS ==========
            [['nome', 'email', 'senha_hash'], 'required'],
            
            // ========== EMAIL ==========
            [['email'], 'email'],
            [['email'], 'unique'],
            
            // ========== STRINGS ==========
            [['nome', 'email'], 'string', 'max' => 255],
            [['cpf'], 'string', 'max' => 14],
            [['telefone'], 'string', 'max' => 15],
            [['tipo'], 'string', 'max' => 20],
            [['auth_key'], 'string', 'max' => 32],
            [['access_token'], 'string', 'max' => 255],
            [['ultimo_login_ip'], 'string', 'max' => 45],
            
            // ========== TIPO ==========
            [['tipo'], 'default', 'value' => self::TIPO_COMERCIAL],
            [['tipo'], 'in', 'range' => [
                self::TIPO_COMERCIAL,
                self::TIPO_ADMIN,
                self::TIPO_SUPORTE
            ]],
            
            // ========== STATUS ==========
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ATIVO],
            [['status'], 'in', 'range' => [
                self::STATUS_ATIVO,
                self::STATUS_INATIVO,
                self::STATUS_BLOQUEADO
            ]],
            
            // ========== DATAS ==========
            [['ultimo_login_at', 'access_token_expires_at', 'created_at', 'updated_at'], 'safe'],
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
            'tipo' => 'Tipo',
            'status' => 'Status',
            'ultimo_login_at' => 'Último Login',
            'created_at' => 'Cadastro',
        ];
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

    // ========== MÉTODOS DE AUTENTICAÇÃO ==========
    
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString(32);
        $this->access_token_expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
        $this->save(false);
        return $this->access_token;
    }

    public function isTokenValido()
    {
        if (empty($this->access_token_expires_at)) {
            return false;
        }
        return strtotime($this->access_token_expires_at) > time();
    }

    // ========== MÉTODOS DE STATUS ==========
    
    public function isAtivo()
    {
        return $this->status == self::STATUS_ATIVO;
    }

    public function isAdmin()
    {
        return $this->tipo === self::TIPO_ADMIN;
    }

    // ========== MÉTODOS DE LOGIN ==========
    
    public function registrarLogin()
    {
        $this->ultimo_login_at = date('Y-m-d H:i:s');
        $this->ultimo_login_ip = Yii::$app->request->userIP;
        $this->save(false);
    }

    // ========== IDENTITY INTERFACE ==========
    
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ATIVO]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token,
            'status' => self::STATUS_ATIVO
        ]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
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

    // ========== QUERIES ==========
    
    public static function find()
    {
        return parent::find()->andWhere(['deleted_at' => null]);
    }
}