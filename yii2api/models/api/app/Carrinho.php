<?php
// models/api/app/Carrinho.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class Carrinho
 * 
 * @property int $id
 * @property int $usuario_id
 * @property int $loja_id
 * @property string $status
 * @property int $total_itens
 * @property float $subtotal
 * @property string|null $metadata
 * @property string $criado_em
 * @property string $atualizado_em
 * 
 * @property AppUsuario $usuario
 * @property Loja $loja
 * @property CarrinhoItem[] $itens
 */
class Carrinho extends ActiveRecord
{
    const STATUS_ATIVO = 'ativo';
    const STATUS_FINALIZADO = 'finalizado';
    const STATUS_ABANDONADO = 'abandonado';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carrinho';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'loja_id'], 'required'],
            [['usuario_id', 'loja_id', 'total_itens'], 'integer'],
            [['subtotal'], 'number'],
            [['metadata'], 'safe'],
            [['criado_em', 'atualizado_em'], 'safe'],
            [['status'], 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_FINALIZADO, self::STATUS_ABANDONADO]],
            [['status'], 'default', 'value' => self::STATUS_ATIVO],
            [['total_itens'], 'default', 'value' => 0],
            [['subtotal'], 'default', 'value' => 0],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::class, 'targetAttribute' => ['usuario_id' => 'id']],
            [['loja_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loja::class, 'targetAttribute' => ['loja_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuário',
            'loja_id' => 'Loja',
            'status' => 'Status',
            'total_itens' => 'Total de Itens',
            'subtotal' => 'Subtotal',
            'metadata' => 'Metadados',
            'criado_em' => 'Criado Em',
            'atualizado_em' => 'Atualizado Em',
        ];
    }
    
    /**
     * Gets query for [[Usuario]].
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::class, ['id' => 'usuario_id']);
    }
    
    /**
     * Gets query for [[Loja]].
     */
    public function getLoja()
    {
        return $this->hasOne(Loja::class, ['id' => 'loja_id']);
    }
    
    /**
     * Gets query for [[Itens]].
     */
    public function getItens()
    {
        return $this->hasMany(CarrinhoItem::class, ['carrinho_id' => 'id']);
    }
    
    /**
     * Verifica se o carrinho está vazio
     */
    public function isEmpty()
    {
        return $this->total_itens == 0;
    }
    
    /**
     * Retorna o metadata decodificado
     */
    public function getMetadataDecoded()
    {
        if ($this->metadata) {
            return json_decode($this->metadata, true);
        }
        return [];
    }
}