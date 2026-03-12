<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\lojista\LojistaUsuario;

/**
 * This is the model class for table "lojista_lojas".
 * Tabela pivot entre lojistas e lojas (muitos-para-muitos)
 *
 * @property int $id
 * @property int $lojista_id
 * @property int $loja_id
 * @property string $permissao
 * @property string $criado_em
 *
 * @property LojistaUsuario $lojista
 * @property Loja $loja
 */
class LojistaLoja extends ActiveRecord
{
    const PERMISSAO_ADMIN = 'admin';
    const PERMISSAO_GERENTE = 'gerente';
    const PERMISSAO_VENDEDOR = 'vendedor';
    const PERMISSAO_ENTREGADOR = 'entregador';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lojista_lojas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lojista_id', 'loja_id'], 'required'],
            [['lojista_id', 'loja_id'], 'integer'],
            [['permissao'], 'string', 'max' => 20],
            [['permissao'], 'default', 'value' => self::PERMISSAO_VENDEDOR],
            [['permissao'], 'in', 'range' => [
                self::PERMISSAO_ADMIN,
                self::PERMISSAO_GERENTE,
                self::PERMISSAO_VENDEDOR,
                self::PERMISSAO_ENTREGADOR,
            ]],
            [['lojista_id', 'loja_id'], 'unique', 'targetAttribute' => ['lojista_id', 'loja_id']],
            [['criado_em'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lojista_id' => 'Lojista',
            'loja_id' => 'Loja',
            'permissao' => 'Permissão',
            'criado_em' => 'Vinculado em',
        ];
    }

    /**
     * Gets query for [[Lojista]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLojista()
    {
        return $this->hasOne(LojistaUsuario::class, ['id' => 'lojista_id']);
    }

    /**
     * Gets query for [[Loja]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoja()
    {
        return $this->hasOne(Loja::class, ['id' => 'loja_id']);
    }

    /**
     * Retorna lista de permissões
     */
    public static function getPermissaoList()
    {
        return [
            self::PERMISSAO_ADMIN => 'Administrador',
            self::PERMISSAO_GERENTE => 'Gerente',
            self::PERMISSAO_VENDEDOR => 'Vendedor',
            self::PERMISSAO_ENTREGADOR => 'Entregador',
        ];
    }

    /**
     * Retorna texto da permissão
     */
    public function getPermissaoTexto()
    {
        $list = self::getPermissaoList();
        return $list[$this->permissao] ?? 'Desconhecido';
    }
}