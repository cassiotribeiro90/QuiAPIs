<?php
// models/api/gestor/Produto.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "produto".
 *
 * @property int $id
 * @property int $loja_id
 * @property int|null $subcategoria_id
 * @property string $nome
 * @property string|null $descricao
 * @property string $slug
 * @property float $preco
 * @property float|null $preco_promocional
 * @property string|null $imagem
 * @property array|null $imagens
 * @property array|null $ingredientes
 * @property string|null $ingredientes_texto
 * @property int|null $calorias
 * @property int|null $peso_gramas
 * @property int $contem_gluten
 * @property int $contem_lactose
 * @property int $vegano
 * @property int $vegetariano
 * @property int $apimentado
 * @property array|null $selos
 * @property string|null $disponivel_inicio
 * @property string|null $disponivel_fim
 * @property array|null $disponivel_dias
 * @property string|null $ultima_venda_em
 * @property int $vendas_hoje
 * @property array|null $variacoes
 * @property array|null $opcoes
 * @property int|null $tempo_preparo_min
 * @property int $disponivel
 * @property int $estoque
 * @property int $ordem
 * @property float $nota_media
 * @property int $total_avaliacoes
 * @property int $visualizacoes
 * @property int $cliques
 * @property int $ativo
 * @property int $destaque
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 */
class Produto extends ActiveRecord
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;

    public static function tableName()
    {
        return '{{%produto}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'nome',
                'ensureUnique' => true,
                'immutable' => true,
            ],
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
            [['loja_id', 'nome', 'preco'], 'required'],
            [['loja_id', 'subcategoria_id', 'calorias', 'peso_gramas', 'vendas_hoje', 'tempo_preparo_min', 'estoque', 'ordem', 'total_avaliacoes', 'visualizacoes', 'cliques'], 'integer'],
            [['preco', 'preco_promocional', 'nota_media'], 'number'],
            [['descricao', 'ingredientes_texto', 'imagens', 'ingredientes', 'selos', 'disponivel_dias', 'variacoes', 'opcoes'], 'safe'],
            [['contem_gluten', 'contem_lactose', 'vegano', 'vegetariano', 'apimentado', 'disponivel', 'ativo', 'destaque'], 'boolean'],
            [['disponivel_inicio', 'disponivel_fim', 'ultima_venda_em', 'criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
            [['nome', 'slug'], 'string', 'max' => 255],
            [['imagem'], 'string', 'max' => 500],
            [['slug'], 'unique'],
            [['loja_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loja::class, 'targetAttribute' => ['loja_id' => 'id']],
            [['subcategoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subcategoria::class, 'targetAttribute' => ['subcategoria_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loja_id' => 'Loja',
            'subcategoria_id' => 'Subcategoria',
            'nome' => 'Nome',
            'descricao' => 'Descrição',
            'slug' => 'Slug',
            'preco' => 'Preço',
            'preco_promocional' => 'Preço Promocional',
            'imagem' => 'Imagem Principal',
            'imagens' => 'Galeria',
            'ingredientes' => 'Ingredientes',
            'ingredientes_texto' => 'Ingredientes (texto)',
            'calorias' => 'Calorias',
            'peso_gramas' => 'Peso (g)',
            'contem_gluten' => 'Contém Glúten',
            'contem_lactose' => 'Contém Lactose',
            'vegano' => 'Vegano',
            'vegetariano' => 'Vegetariano',
            'apimentado' => 'Apimentado',
            'selos' => 'Selos',
            'disponivel_inicio' => 'Disponível a partir de',
            'disponivel_fim' => 'Disponível até',
            'disponivel_dias' => 'Dias disponíveis',
            'ultima_venda_em' => 'Última venda',
            'vendas_hoje' => 'Vendas hoje',
            'variacoes' => 'Variações',
            'opcoes' => 'Opções',
            'tempo_preparo_min' => 'Tempo de preparo (min)',
            'disponivel' => 'Disponível',
            'estoque' => 'Estoque',
            'ordem' => 'Ordem',
            'nota_media' => 'Nota média',
            'total_avaliacoes' => 'Total avaliações',
            'visualizacoes' => 'Visualizações',
            'cliques' => 'Cliques',
            'ativo' => 'Ativo',
            'destaque' => 'Destaque',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }

    public static function find()
    {
        return parent::find()->andWhere(['deletado_em' => null]);
    }

    public function softDelete()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    public function isDisponivel()
    {
        return $this->disponivel && $this->ativo && $this->deletado_em === null;
    }

    // Relacionamentos
    public function getLoja()
    {
        return $this->hasOne(Loja::class, ['id' => 'loja_id']);
    }

    public function getSubcategoria()
    {
        return $this->hasOne(Subcategoria::class, ['id' => 'subcategoria_id']);
    }
}