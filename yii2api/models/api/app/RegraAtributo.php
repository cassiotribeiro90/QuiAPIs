<?php
// models/api/app/RegraAtributo.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class RegraAtributo
 * 
 * @property int $id
 * @property string $regra_tipo  // requer, bloqueia, sugere
 * @property int $opcao_id
 * @property int|null $opcao_requerida_id
 * @property int|null $categoria_requerida_id
 * @property int|null $valor_min
 * @property int|null $valor_max
 * @property string|null $mensagem
 * @property string $criado_em
 * 
 * @property AtributoOpcao $opcao
 * @property AtributoOpcao $opcaoRequerida
 * @property AtributoCategoria $categoriaRequerida
 */
class RegraAtributo extends ActiveRecord
{
    const REGRA_REQUER = 'requer';
    const REGRA_BLOQUEIA = 'bloqueia';
    const REGRA_SUGERE = 'sugere';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regra_atributo';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['regra_tipo', 'opcao_id'], 'required'],
            [['opcao_id', 'opcao_requerida_id', 'categoria_requerida_id', 'valor_min', 'valor_max'], 'integer'],
            [['regra_tipo'], 'in', 'range' => [self::REGRA_REQUER, self::REGRA_BLOQUEIA, self::REGRA_SUGERE]],
            [['mensagem'], 'string'],
            [['criado_em'], 'safe'],
            [['opcao_id'], 'exist', 'skipOnError' => true, 'targetClass' => AtributoOpcao::class, 'targetAttribute' => ['opcao_id' => 'id']],
            [['opcao_requerida_id'], 'exist', 'skipOnError' => true, 'targetClass' => AtributoOpcao::class, 'targetAttribute' => ['opcao_requerida_id' => 'id']],
            [['categoria_requerida_id'], 'exist', 'skipOnError' => true, 'targetClass' => AtributoCategoria::class, 'targetAttribute' => ['categoria_requerida_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'regra_tipo' => 'Tipo de Regra',
            'opcao_id' => 'Opção',
            'opcao_requerida_id' => 'Opção Requerida',
            'categoria_requerida_id' => 'Categoria Requerida',
            'valor_min' => 'Valor Mínimo',
            'valor_max' => 'Valor Máximo',
            'mensagem' => 'Mensagem',
            'criado_em' => 'Criado Em',
        ];
    }
    
    /**
     * Gets query for [[Opcao]].
     */
    public function getOpcao()
    {
        return $this->hasOne(AtributoOpcao::class, ['id' => 'opcao_id']);
    }
    
    /**
     * Gets query for [[OpcaoRequerida]].
     */
    public function getOpcaoRequerida()
    {
        return $this->hasOne(AtributoOpcao::class, ['id' => 'opcao_requerida_id']);
    }
    
    /**
     * Gets query for [[CategoriaRequerida]].
     */
    public function getCategoriaRequerida()
    {
        return $this->hasOne(AtributoCategoria::class, ['id' => 'categoria_requerida_id']);
    }
}