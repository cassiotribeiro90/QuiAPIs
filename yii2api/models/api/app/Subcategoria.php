<?php
// models/api/app/Subcategoria.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class Subcategoria
 * 
 * @property int $id
 * @property string $nome
 * @property string|null $icone
 * @property int $ordem
 * @property string $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * 
 * @property Produto[] $produtos
 */
class Subcategoria extends ActiveRecord
{
    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subcategoria';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['ordem'], 'integer'],
            [['nome'], 'string', 'max' => 100],
            [['icone'], 'string', 'max' => 10],
            [['status'], 'string', 'max' => 20],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'default', 'value' => self::STATUS_ATIVO],
            [['ordem'], 'default', 'value' => 0],
        ];
    }
    
    /**
     * Gets query for [[Produtos]].
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['subcategoria_id' => 'id']);
    }
}