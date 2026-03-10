<?php
// models/api/gestor/Categoria.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "categoria".
 *
 * @property int $id
 * @property string $nome
 * @property string $slug
 * @property string|null $descricao
 * @property string|null $icone
 * @property string|null $imagem
 * @property string $cor
 * @property int $ordem
 * @property int $ativo
 * @property int $destaque
 * @property array|null $metadata
 * @property string $criado_em
 * @property string $atualizado_em
 */
class Categoria extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%categoria}}';
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
            [['nome'], 'required'],
            [['descricao', 'metadata'], 'safe'],
            [['ordem', 'ativo', 'destaque'], 'integer'],
            [['criado_em', 'atualizado_em'], 'safe'],
            [['nome', 'slug', 'icone'], 'string', 'max' => 100],
            [['imagem'], 'string', 'max' => 500],
            [['cor'], 'string', 'max' => 7],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'slug' => 'Slug',
            'descricao' => 'Descrição',
            'icone' => 'Ícone',
            'imagem' => 'Imagem',
            'cor' => 'Cor',
            'ordem' => 'Ordem',
            'ativo' => 'Ativo',
            'destaque' => 'Destaque',
            'metadata' => 'Metadados',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
        ];
    }

    /**
     * Relacionamento: uma categoria tem muitas subcategorias
     */
    public function getSubcategorias()
    {
        return $this->hasMany(Subcategoria::class, ['categoria_id' => 'id']);
    }

    /**
     * Query para apenas categorias ativas
     */
    public static function findAtivas()
    {
        return self::find()->where(['ativo' => 1]);
    }
}