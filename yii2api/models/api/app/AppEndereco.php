<?php
// models/api/app/AppEndereco.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "app_endereco".
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $tipo
 * @property string|null $apelido
 * @property string $cep
 * @property string $logradouro
 * @property string $numero
 * @property string|null $complemento
 * @property string $bairro
 * @property string $cidade
 * @property string $uf
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $referencia
 * @property string|null $destinatario
 * @property string|null $telefone_contato
 * @property int $padrao
 * @property int $ativo
 * @property array|null $metadata
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 */
class AppEndereco extends ActiveRecord
{
    const TIPO_RESIDENCIAL = 'residencial';
    const TIPO_COMERCIAL = 'comercial';
    const TIPO_ENTREGA = 'entrega';
    const TIPO_COBRANCA = 'cobranca';

    public static function tableName()
    {
        return '{{%app_endereco}}';
    }

    public function rules()
    {
        return [
            [['usuario_id', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'], 'required'],
            [['usuario_id', 'padrao', 'ativo'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['metadata', 'referencia', 'complemento'], 'safe'],
            [['criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
            [['tipo', 'apelido', 'destinatario', 'telefone_contato'], 'string', 'max' => 100],
            [['cep'], 'string', 'max' => 9],
            [['logradouro', 'bairro', 'cidade'], 'string', 'max' => 255],
            [['numero'], 'string', 'max' => 20],
            [['uf'], 'string', 'max' => 2],
            [['tipo'], 'in', 'range' => [self::TIPO_RESIDENCIAL, self::TIPO_COMERCIAL, self::TIPO_ENTREGA, self::TIPO_COBRANCA]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuário',
            'tipo' => 'Tipo',
            'apelido' => 'Apelido',
            'cep' => 'CEP',
            'logradouro' => 'Logradouro',
            'numero' => 'Número',
            'complemento' => 'Complemento',
            'bairro' => 'Bairro',
            'cidade' => 'Cidade',
            'uf' => 'UF',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'referencia' => 'Ponto de Referência',
            'destinatario' => 'Destinatário',
            'telefone_contato' => 'Telefone Contato',
            'padrao' => 'Padrão',
            'ativo' => 'Ativo',
            'metadata' => 'Metadados',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }

    public function getUsuario()
    {
        return $this->hasOne(Usuario::class, ['id' => 'usuario_id']);
    }

    public function getEnderecoCompleto()
    {
        $endereco = "{$this->logradouro}, {$this->numero}";
        if ($this->complemento) {
            $endereco .= " - {$this->complemento}";
        }
        $endereco .= ", {$this->bairro}, {$this->cidade}/{$this->uf} - CEP: {$this->cep}";
        return $endereco;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Se este endereço foi marcado como padrão, remove padrão dos outros
        if ($this->padrao == 1) {
            self::updateAll(
                ['padrao' => 0],
                ['and', ['usuario_id' => $this->usuario_id], ['!=', 'id', $this->id]]
            );
        }
    }

    public function softDelete()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    public static function find()
    {
        return parent::find()->andWhere(['deletado_em' => null]);
    }
}