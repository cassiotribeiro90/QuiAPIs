<?php
// models/api/gestor/StoreUsuarioLoja.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "store_usuario_loja".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $loja_id
 * @property string $funcao  // proprietario, gerente, vendedor (pode ser diferente por loja)
 * @property int $status
 * @property array|null $permissoes
 * @property string|null $ultimo_acesso_em
 * @property string $criado_em
 * @property string $atualizado_em
 */
class StoreUsuarioLoja extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%store_usuario_loja}}';
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
            [['usuario_id', 'loja_id', 'funcao'], 'required'],
            [['usuario_id', 'loja_id', 'status'], 'integer'],
            [['permissoes', 'ultimo_acesso_em'], 'safe'],
            [['funcao'], 'string'],
            [['usuario_id', 'loja_id'], 'unique', 'targetAttribute' => ['usuario_id', 'loja_id'], 'message' => 'Usuário já está vinculado a esta loja.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuário',
            'loja_id' => 'Loja',
            'funcao' => 'Função',
            'status' => 'Status',
            'permissoes' => 'Permissões',
            'ultimo_acesso_em' => 'Último Acesso',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
        ];
    }

    public function getUsuario()
    {
        return $this->hasOne(StoreUsuario::class, ['id' => 'usuario_id']);
    }

    public function getLoja()
    {
        return $this->hasOne(Loja::class, ['id' => 'loja_id']);
    }
}