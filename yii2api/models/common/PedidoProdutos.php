<?php
namespace app\models\common;

use Yii;
use yii\db\ActiveRecord;

/**
 * Model para a tabela pedido_produtos (pivot)
 * 
 * @property int $pedido_id
 * @property int $produto_id
 * @property int $quantidade
 * @property float $preco_unitario
 * @property float $subtotal
 */
class PedidoProduto extends ActiveRecord
{
    public static function tableName()
    {
        return 'pedido_produtos';
    }

    public function rules()
    {
        return [
            [['pedido_id', 'produto_id', 'quantidade', 'preco_unitario'], 'required'],
            [['pedido_id', 'produto_id', 'quantidade'], 'integer'],
            [['quantidade'], 'integer', 'min' => 1],
            [['preco_unitario', 'subtotal'], 'number', 'min' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pedido_id' => 'Pedido',
            'produto_id' => 'Produto',
            'quantidade' => 'Quantidade',
            'preco_unitario' => 'Preço Unitário',
            'subtotal' => 'Subtotal',
        ];
    }

    /**
     * Antes de salvar, calcula subtotal
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->subtotal = $this->quantidade * $this->preco_unitario;
            return true;
        }
        return false;
    }

    /**
     * Relação com pedido
     */
    public function getPedido()
    {
        return $this->hasOne(Pedido::class, ['id' => 'pedido_id']);
    }

    /**
     * Relação com produto
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::class, ['id' => 'produto_id']);
    }
}