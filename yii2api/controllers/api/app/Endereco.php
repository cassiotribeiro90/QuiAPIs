<?php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "app_endereco".
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $tipo                'residencial','comercial','entrega','cobranca'
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
 * @property int $padrao               0/1
 * @property int $ativo                0/1
 * @property string|null $criado_em
 * @property string|null $atualizado_em
 * @property string|null $deletado_em
 *
 * @property Usuario $usuario
 */
class Endereco extends ActiveRecord
{
    // Constantes para tipo
    const TIPO_RESIDENCIAL = 'residencial';
    const TIPO_COMERCIAL   = 'comercial';
    const TIPO_ENTREGA     = 'entrega';
    const TIPO_COBRANCA    = 'cobranca';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'app_endereco';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Obrigatórios
            [['usuario_id', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'], 'required'],
            
            // Tipos
            [['usuario_id', 'padrao', 'ativo'], 'integer'],
            ['tipo', 'in', 'range' => [self::TIPO_RESIDENCIAL, self::TIPO_COMERCIAL, self::TIPO_ENTREGA, self::TIPO_COBRANCA]],
            ['tipo', 'default', 'value' => self::TIPO_ENTREGA],
            
            // Coordenadas
            [['latitude', 'longitude'], 'number'],
            
            // Strings e limites
            [['apelido'], 'string', 'max' => 50],
            [['cep'], 'string', 'max' => 9],
            [['logradouro', 'complemento', 'referencia'], 'string', 'max' => 255],
            [['numero', 'telefone_contato'], 'string', 'max' => 20],
            [['bairro', 'cidade', 'destinatario'], 'string', 'max' => 100],
            [['uf'], 'string', 'max' => 2],
            
            // Padrões e ativo
            [['padrao', 'ativo'], 'default', 'value' => 1],
            ['padrao', 'in', 'range' => [0, 1]],
            ['ativo', 'in', 'range' => [0, 1]],
            
            // Datas
            [['criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
            'referencia' => 'Referência',
            'destinatario' => 'Destinatário',
            'telefone_contato' => 'Telefone de Contato',
            'padrao' => 'Padrão',
            'ativo' => 'Ativo',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::class, ['id' => 'usuario_id']);
    }

    /**
     * Retorna o endereço formatado em uma única linha.
     * Ex: "Rua X, 123 - Centro, Belo Horizonte - MG"
     *
     * @return string
     */
    public function getEnderecoCompleto()
    {
        $partes = [
            $this->logradouro . ', ' . $this->numero,
            $this->complemento,
            $this->bairro,
            $this->cidade . ' - ' . $this->uf,
            $this->referencia ? 'Ref.: ' . $this->referencia : null,
        ];
        
        return implode(' - ', array_filter($partes));
    }

    /**
     * Retorna o endereço resumido (logradouro, número - bairro).
     *
     * @return string
     */
    public function getEnderecoResumido()
    {
        return $this->logradouro . ', ' . $this->numero . ' - ' . $this->bairro;
    }

    /**
     * Verifica se este é o endereço padrão do usuário.
     *
     * @return bool
     */
    public function isPadrao()
    {
        return $this->padrao == 1;
    }

    /**
     * Verifica se o endereço está ativo.
     *
     * @return bool
     */
    public function isAtivo()
    {
        return $this->ativo == 1;
    }

    /**
     * Define este endereço como padrão, removendo o padrão de outros endereços do mesmo usuário.
     *
     * @return bool
     */
    public function setComoPadrao()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Remove o padrão de outros endereços
            self::updateAll(
                ['padrao' => 0],
                ['usuario_id' => $this->usuario_id]
            );
            
            $this->padrao = 1;
            $result = $this->save(false);
            
            $transaction->commit();
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * Antes de salvar, limpa o CEP (remove traço).
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->cep = preg_replace('/\D/', '', $this->cep);
            return true;
        }
        return false;
    }

    /**
     * Busca endereços de um usuário (excluindo os deletados).
     *
     * @param int $usuarioId
     * @return \yii\db\ActiveQuery
     */
    public static function findDoUsuario($usuarioId)
    {
        return self::find()
            ->where(['usuario_id' => $usuarioId])
            ->andWhere(['deletado_em' => null])
            ->andWhere(['ativo' => 1])
            ->orderBy(['padrao' => SORT_DESC, 'criado_em' => SORT_DESC]);
    }

    /**
     * Soft delete.
     *
     * @return bool
     */
    public function softDelete()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        $this->ativo = 0;
        $this->padrao = 0;
        return $this->save(false);
    }
}