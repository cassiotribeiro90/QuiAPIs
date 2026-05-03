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
 * @property int $quantidade
 * @property float $preco_unitario
 * @property float $preco_total
 * @property string $produto_nome
 * @property string|null $produto_descricao
 * @property string|null $produto_imagem
 * @property array|null $opcoes
 * @property string|null $observacao
 * @property int|null $avaliacao_nota
 * @property string|null $avaliacao_comentario
 * @property array|null $metadata
 * @property string $criado_em
 * @property string $atualizado_em
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
            // Campos obrigatórios (substituído 'nome' por 'produto_nome')
            [['pedido_id', 'produto_id', 'produto_nome', 'quantidade', 'preco_unitario', 'preco_total'], 'required'],
            
            // Inteiros
            [['pedido_id', 'produto_id', 'quantidade', 'avaliacao_nota'], 'integer'],
            
            // Decimais
            [['preco_unitario', 'preco_total'], 'number'],
            
            // Textos longos
            [['produto_descricao', 'observacao', 'avaliacao_comentario'], 'string'],
            
            // JSON / arrays
            [['opcoes', 'metadata'], 'safe'],
            
            // Strings limitadas
            [['produto_nome'], 'string', 'max' => 255],
            [['produto_imagem'], 'string', 'max' => 500],
            
            // Datas
            [['criado_em', 'atualizado_em'], 'safe'],
            
            // Chaves estrangeiras
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
            'quantidade' => 'Quantidade',
            'preco_unitario' => 'Preço Unitário',
            'preco_total' => 'Preço Total',
            'produto_nome' => 'Nome do Produto',
            'produto_descricao' => 'Descrição do Produto',
            'produto_imagem' => 'Imagem do Produto',
            'opcoes' => 'Opções',
            'observacao' => 'Observação',
            'avaliacao_nota' => 'Nota da Avaliação',
            'avaliacao_comentario' => 'Comentário da Avaliação',
            'metadata' => 'Metadata',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
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