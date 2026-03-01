<?php
namespace app\models\app;

use Yii;
use yii\db\ActiveRecord;

class AppLoja extends ActiveRecord
{
    public static function tableName()
    {
        return 'lojas';
    }

    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['descricao', 'endereco', 'horario_funcionamento', 'formas_pagamento'], 'safe'],
            [['nome', 'categoria', 'logo', 'capa'], 'string', 'max' => 500],
            [['nota'], 'number', 'min' => 0, 'max' => 5],
            [['tempo_entrega_min', 'tempo_entrega_max'], 'integer'],
            [['taxa_entrega', 'pedido_minimo'], 'number'],
            [['ativo', 'destaque'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome da Loja',
            'descricao' => 'Descrição',
            'categoria' => 'Categoria',
            'nota' => 'Avaliação',
            'tempo_entrega_min' => 'Tempo Mínimo (min)',
            'tempo_entrega_max' => 'Tempo Máximo (min)',
            'taxa_entrega' => 'Taxa de Entrega (R$)',
            'pedido_minimo' => 'Pedido Mínimo (R$)',
            'ativo' => 'Ativo',
            'destaque' => 'Destaque',
        ];
    }

    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['loja_id' => 'id']);
    }
}