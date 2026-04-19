<?php
// models/api/app/AtributoCategoria.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class AtributoCategoria
 * 
 * @property int $id
 * @property string $nome
 * @property string|null $descricao
 * @property string $tipo_selecao  // unica, multipla, quantidade, fracionado
 * @property int $obrigatorio
 * @property int|null $minimo
 * @property int|null $maximo
 * @property string|null $icone
 * @property int $ordem
 * @property int $ativo
 * @property string $criado_em
 * @property string $atualizado_em
 * 
 * @property AtributoOpcao[] $opcoes
 */
class AtributoCategoria extends ActiveRecord
{
    const TIPO_UNICA = 'unica';
    const TIPO_MULTIPLA = 'multipla';
    const TIPO_QUANTIDADE = 'quantidade';
    const TIPO_FRACIONADO = 'fracionado';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'atributo_categoria';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'tipo_selecao'], 'required'],
            [['obrigatorio', 'minimo', 'maximo', 'ordem', 'ativo'], 'integer'],
            [['nome', 'descricao', 'icone'], 'string'],
            [['tipo_selecao'], 'in', 'range' => [self::TIPO_UNICA, self::TIPO_MULTIPLA, self::TIPO_QUANTIDADE, self::TIPO_FRACIONADO]],
            [['criado_em', 'atualizado_em'], 'safe'],
            [['ativo'], 'default', 'value' => 1],
            [['ordem'], 'default', 'value' => 0],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'descricao' => 'Descrição',
            'tipo_selecao' => 'Tipo de Seleção',
            'obrigatorio' => 'Obrigatório',
            'minimo' => 'Mínimo',
            'maximo' => 'Máximo',
            'icone' => 'Ícone',
            'ordem' => 'Ordem',
            'ativo' => 'Ativo',
            'criado_em' => 'Criado Em',
            'atualizado_em' => 'Atualizado Em',
        ];
    }
    
    /**
     * Gets query for [[Opcoes]].
     */
    public function getOpcoes()
    {
        return $this->hasMany(AtributoOpcao::class, ['categoria_id' => 'id']);
    }
    
    /**
     * Gets query for opções disponíveis
     */
    public function getOpcoesDisponiveis()
    {
        return $this->getOpcoes()->where(['disponivel' => 1])->orderBy(['ordem' => SORT_ASC]);
    }
}