<?php
// models/api/app/AtributoOpcao.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class AtributoOpcao
 * 
 * @property int $id
 * @property int $categoria_id
 * @property string $nome
 * @property string|null $descricao
 * @property float $preco_adicional
 * @property string|null $icone
 * @property string|null $imagem
 * @property string|null $cor
 * @property int $disponivel
 * @property int $estoque
 * @property int $ordem
 * @property string $criado_em
 * @property string $atualizado_em
 * 
 * @property AtributoCategoria $categoria
 * @property ProdutoOpcaoAdicional[] $produtoOpcoes
 * @property RegraAtributo[] $regras
 */
class AtributoOpcao extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'atributo_opcao';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['categoria_id', 'nome'], 'required'],
            [['categoria_id', 'disponivel', 'estoque', 'ordem'], 'integer'],
            [['preco_adicional'], 'number'],
            [['nome', 'descricao', 'icone', 'imagem', 'cor'], 'string'],
            [['criado_em', 'atualizado_em'], 'safe'],
            [['disponivel'], 'default', 'value' => 1],
            [['ordem'], 'default', 'value' => 0],
            [['categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => AtributoCategoria::class, 'targetAttribute' => ['categoria_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'categoria_id' => 'Categoria',
            'nome' => 'Nome',
            'descricao' => 'Descrição',
            'preco_adicional' => 'Preço Adicional',
            'icone' => 'Ícone',
            'imagem' => 'Imagem',
            'cor' => 'Cor',
            'disponivel' => 'Disponível',
            'estoque' => 'Estoque',
            'ordem' => 'Ordem',
            'criado_em' => 'Criado Em',
            'atualizado_em' => 'Atualizado Em',
        ];
    }
    
    /**
     * Gets query for [[Categoria]].
     */
    public function getCategoria()
    {
        return $this->hasOne(AtributoCategoria::class, ['id' => 'categoria_id']);
    }
    
    /**
     * Gets query for [[ProdutoOpcoes]].
     */
    public function getProdutoOpcoes()
    {
        return $this->hasMany(ProdutoOpcaoAdicional::class, ['opcao_id' => 'id']);
    }
    
    /**
     * Gets query for [[Regras]].
     */
    public function getRegras()
    {
        return $this->hasMany(RegraAtributo::class, ['opcao_id' => 'id']);
    }
}