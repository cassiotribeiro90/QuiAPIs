<?php
// models/api/app/ProdutoOpcaoAdicional.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class ProdutoOpcaoAdicional
 * 
 * @property int $id
 * @property int $produto_id
 * @property int $opcao_id
 * @property float|null $preco_adicional
 * @property int $disponivel
 * @property string $criado_em
 * 
 * @property Produto $produto
 * @property AtributoOpcao $opcao
 */
class ProdutoOpcaoAdicional extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'produto_opcao_adicional';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['produto_id', 'opcao_id'], 'required'],
            [['produto_id', 'opcao_id', 'disponivel'], 'integer'],
            [['preco_adicional'], 'number'],
            [['criado_em'], 'safe'],
            [['disponivel'], 'default', 'value' => 1],
            [['produto_id', 'opcao_id'], 'unique', 'targetAttribute' => ['produto_id', 'opcao_id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::class, 'targetAttribute' => ['produto_id' => 'id']],
            [['opcao_id'], 'exist', 'skipOnError' => true, 'targetClass' => AtributoOpcao::class, 'targetAttribute' => ['opcao_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produto_id' => 'Produto',
            'opcao_id' => 'Opção',
            'preco_adicional' => 'Preço Adicional',
            'disponivel' => 'Disponível',
            'criado_em' => 'Criado Em',
        ];
    }
    
    /**
     * Gets query for [[Produto]].
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::class, ['id' => 'produto_id']);
    }
    
    /**
     * Gets query for [[Opcao]].
     */
    public function getOpcao()
    {
        return $this->hasOne(AtributoOpcao::class, ['id' => 'opcao_id']);
    }
    
    /**
     * Retorna o preço efetivo (se tiver preço específico no produto, usa ele, senão o da opção)
     */
    public function getPrecoEfetivo()
    {
        if ($this->preco_adicional !== null) {
            return (float)$this->preco_adicional;
        }
        return $this->opcao ? (float)$this->opcao->preco_adicional : 0;
    }
}