<?php
// models/api/app/CarrinhoItem.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class CarrinhoItem
 * 
 * @property int $id
 * @property int $carrinho_id
 * @property int $produto_id
 * @property int $quantidade
 * @property float $preco_unitario
 * @property float $preco_adicionais
 * @property float $preco_total
 * @property string $produto_nome
 * @property string|null $produto_descricao
 * @property string|null $produto_imagem
 * @property string|null $opcoes
 * @property string|null $opcoes_detalhes
 * @property string|null $observacao
 * @property string|null $metadata
 * @property string $criado_em
 * 
 * @property Carrinho $carrinho
 * @property Produto $produto
 */
class CarrinhoItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carrinho_item';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carrinho_id', 'produto_id', 'produto_nome', 'preco_unitario', 'preco_total'], 'required'],
            [['carrinho_id', 'produto_id', 'quantidade'], 'integer'],
            [['preco_unitario', 'preco_adicionais', 'preco_total'], 'number'],
            [['produto_nome'], 'string', 'max' => 255],
            [['produto_descricao', 'observacao'], 'string'],
            [['produto_imagem'], 'string', 'max' => 500],
            [['opcoes', 'opcoes_detalhes', 'metadata'], 'safe'],
            [['criado_em'], 'safe'],
            [['quantidade'], 'default', 'value' => 1],
            [['preco_adicionais'], 'default', 'value' => 0],
            [['carrinho_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carrinho::class, 'targetAttribute' => ['carrinho_id' => 'id']],
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
            'carrinho_id' => 'Carrinho',
            'produto_id' => 'Produto',
            'quantidade' => 'Quantidade',
            'preco_unitario' => 'Preço Unitário',
            'preco_adicionais' => 'Preço dos Adicionais',
            'preco_total' => 'Preço Total',
            'produto_nome' => 'Nome do Produto',
            'produto_descricao' => 'Descrição',
            'produto_imagem' => 'Imagem',
            'opcoes' => 'Opções (IDs)',
            'opcoes_detalhes' => 'Detalhes das Opções',
            'observacao' => 'Observação',
            'metadata' => 'Metadados',
            'criado_em' => 'Criado Em',
        ];
    }
    
    /**
     * Gets query for [[Carrinho]].
     */
    public function getCarrinho()
    {
        return $this->hasOne(Carrinho::class, ['id' => 'carrinho_id']);
    }
    
    /**
     * Gets query for [[Produto]].
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::class, ['id' => 'produto_id']);
    }
    
    /**
     * Retorna as opções decodificadas
     */
    public function getOpcoesDecoded()
    {
        if ($this->opcoes) {
            return json_decode($this->opcoes, true);
        }
        return [];
    }
    
    /**
     * Retorna os detalhes das opções decodificados
     */
    public function getOpcoesDetalhesDecoded()
    {
        if ($this->opcoes_detalhes) {
            return json_decode($this->opcoes_detalhes, true);
        }
        return [];
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
    
    /**
     * Calcula o preço total do item
     */
    public function calcularPrecoTotal()
    {
        return ($this->preco_unitario + $this->preco_adicionais) * $this->quantidade;
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Garantir que preco_total está correto
            $this->preco_total = $this->calcularPrecoTotal();
            
            // Converter arrays para JSON
            if (is_array($this->opcoes)) {
                $this->opcoes = json_encode($this->opcoes);
            }
            if (is_array($this->opcoes_detalhes)) {
                $this->opcoes_detalhes = json_encode($this->opcoes_detalhes);
            }
            if (is_array($this->metadata)) {
                $this->metadata = json_encode($this->metadata);
            }
            
            return true;
        }
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        
        // Converter JSON para array após buscar
        if ($this->opcoes && is_string($this->opcoes)) {
            $this->opcoes = json_decode($this->opcoes, true);
        }
        if ($this->opcoes_detalhes && is_string($this->opcoes_detalhes)) {
            $this->opcoes_detalhes = json_decode($this->opcoes_detalhes, true);
        }
        if ($this->metadata && is_string($this->metadata)) {
            $this->metadata = json_decode($this->metadata, true);
        }
    }
}