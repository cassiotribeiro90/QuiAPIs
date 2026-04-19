<?php
// models/api/app/PedidoItem.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pedido_item".
 *
 * @property int $id
 * @property int $pedido_id
 * @property int $produto_id
 * @property string $nome
 * @property int $quantidade
 * @property float $preco_unitario
 * @property float $preco_total
 * @property string|null $observacao
 * @property string|null $opcoes
 *
 * @property Pedido $pedido
 * @property Produto $produto
 */
class PedidoItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedido_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pedido_id', 'produto_id', 'nome', 'quantidade', 'preco_unitario', 'preco_total'], 'required'],
            [['pedido_id', 'produto_id', 'quantidade'], 'integer'],
            [['preco_unitario', 'preco_total'], 'number'],
            [['observacao', 'opcoes'], 'string'],
            [['nome'], 'string', 'max' => 255],
            [['pedido_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pedido::class, 'targetAttribute' => ['pedido_id' => 'id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::class, 'targetAttribute' => ['produto_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_id' => 'Pedido',
            'produto_id' => 'Produto',
            'nome' => 'Nome',
            'quantidade' => 'Quantidade',
            'preco_unitario' => 'Preço Unitário',
            'preco_total' => 'Preço Total',
            'observacao' => 'Observação',
            'opcoes' => 'Opções',
        ];
    }

    /**
     * Gets query for [[Pedido]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPedido()
    {
        return $this->hasOne(Pedido::class, ['id' => 'pedido_id']);
    }

    /**
     * Gets query for [[Produto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::class, ['id' => 'produto_id']);
    }
}