<?php
// models/api/gestor/Subcategoria.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subcategoria".
 *
 * @property int $id
 * @property int $categoria_id
 * @property string $nome
 * @property string $slug
 * @property string|null $descricao
 * @property string|null $icone
 * @property string|null $imagem
 * @property int $ordem
 * @property int $ativo
 * @property array|null $metadata
 * @property string $criado_em
 * @property string $atualizado_em
 */
class Subcategoria extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%subcategoria}}';
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
            [['categoria_id', 'nome'], 'required'],
            [['categoria_id', 'ordem', 'ativo'], 'integer'],
            [['descricao', 'metadata'], 'safe'],
            [['criado_em', 'atualizado_em'], 'safe'],
            [['nome', 'slug', 'icone'], 'string', 'max' => 100],
            [['imagem'], 'string', 'max' => 500],
            [['slug'], 'unique'],
            [['categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::class, 'targetAttribute' => ['categoria_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'categoria_id' => 'Categoria',
            'nome' => 'Nome',
            'slug' => 'Slug',
            'descricao' => 'Descrição',
            'icone' => 'Ícone',
            'imagem' => 'Imagem',
            'ordem' => 'Ordem',
            'ativo' => 'Ativo',
            'metadata' => 'Metadados',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
        ];
    }

    /**
     * Relacionamento: pertence a uma categoria
     */
    public function getCategoria()
    {
        return $this->hasOne(Categoria::class, ['id' => 'categoria_id']);
    }

    /**
     * Query para apenas subcategorias ativas
     */
    public static function findAtivas()
    {
        return self::find()->where(['ativo' => 1]);
    }
}