<?php
namespace app\models\common;

use Yii;
use yii\db\ActiveRecord;

/**
 * Model para a tabela app_enderecos
 * 
 * @property int $id
 * @property int $usuario_id
 * @property string $tipo
 * @property string $apelido
 * @property string $cep
 * @property string $logradouro
 * @property string $numero
 * @property string $complemento
 * @property string $bairro
 * @property string $cidade
 * @property string $uf
 * @property string $pais
 * @property float $latitude
 * @property float $longitude
 * @property string $referencia
 * @property string $destinatario
 * @property string $telefone_contato
 * @property bool $padrao
 * @property bool $ativo
 * @property array $metadata
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * 
 * @property AppUsuario $usuario
 */
class AppEndereco extends ActiveRecord
{
    const TIPO_RESIDENCIAL = 'residencial';
    const TIPO_COMERCIAL = 'comercial';
    const TIPO_ENTREGA = 'entrega';
    const TIPO_COBRANCA = 'cobranca';

    public static function tableName()
    {
        return 'app_enderecos';
    }

    public function rules()
    {
        return [
            // ========== OBRIGATÓRIOS ==========
            [['usuario_id', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'], 'required'],
            
            // ========== RELACIONAMENTO ==========
            [['usuario_id'], 'integer'],
            [['usuario_id'], 'exist', 'targetClass' => AppUsuario::class, 'targetAttribute' => 'id'],
            
            // ========== TIPO ==========
            [['tipo'], 'string', 'max' => 20],
            [['tipo'], 'default', 'value' => self::TIPO_ENTREGA],
            [['tipo'], 'in', 'range' => [
                self::TIPO_RESIDENCIAL,
                self::TIPO_COMERCIAL,
                self::TIPO_ENTREGA,
                self::TIPO_COBRANCA
            ]],
            
            // ========== STRINGS ==========
            [['apelido'], 'string', 'max' => 50],
            [['logradouro', 'cidade', 'referencia', 'destinatario'], 'string', 'max' => 255],
            [['numero', 'uf'], 'string', 'max' => 20],
            [['complemento'], 'string', 'max' => 255],
            [['bairro'], 'string', 'max' => 100],
            [['pais'], 'string', 'max' => 50],
            [['pais'], 'default', 'value' => 'Brasil'],
            [['cep'], 'string', 'max' => 9],
            [['telefone_contato'], 'string', 'max' => 20],
            
            // ========== COORDENADAS ==========
            [['latitude', 'longitude'], 'number'],
            
            // ========== BOOLEANOS ==========
            [['padrao', 'ativo'], 'boolean'],
            [['padrao'], 'default', 'value' => false],
            [['ativo'], 'default', 'value' => true],
            
            // ========== JSON ==========
            [['metadata'], 'safe'],
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
            'pais' => 'País',
            'referencia' => 'Ponto de Referência',
            'destinatario' => 'Destinatário',
            'telefone_contato' => 'Telefone de Contato',
            'padrao' => 'Endereço Padrão',
            'ativo' => 'Ativo',
            'created_at' => 'Criado em',
        ];
    }

    /**
     * Relação com usuário
     */
    public function getUsuario()
    {
        return $this->hasOne(AppUsuario::class, ['id' => 'usuario_id']);
    }

    /**
     * Retorna endereço formatado em uma linha
     */
    public function getEnderecoCompleto()
    {
        $partes = [
            $this->logradouro,
            $this->numero ? 'nº ' . $this->numero : null,
            $this->complemento,
            $this->bairro,
            $this->cidade . ' - ' . $this->uf,
            'CEP: ' . $this->cep
        ];
        
        return implode(', ', array_filter($partes));
    }

    /**
     * Retorna endereço formatado para exibição
     */
    public function getEnderecoFormatado()
    {
        $linhas = [
            "{$this->logradouro}, {$this->numero}" . ($this->complemento ? " - {$this->complemento}" : ''),
            $this->bairro,
            "{$this->cidade} - {$this->uf}",
            "CEP: {$this->cep}"
        ];
        
        return implode("\n", $linhas);
    }

    /**
     * Antes de salvar, garante que apenas um endereço seja padrão por usuário
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->padrao) {
                // Remove flag padrão de outros endereços do mesmo usuário
                static::updateAll(
                    ['padrao' => false],
                    ['and', ['usuario_id' => $this->usuario_id], ['!=', 'id', $this->id]]
                );
            }
            return true;
        }
        return false;
    }

    /**
     * Busca endereço padrão do usuário
     */
    public static function findPadraoByUsuario($usuario_id)
    {
        return static::findOne([
            'usuario_id' => $usuario_id,
            'padrao' => true,
            'ativo' => true
        ]);
    }

    /**
     * Busca endereços ativos de um usuário
     */
    public static function findAtivosByUsuario($usuario_id)
    {
        return static::findAll([
            'usuario_id' => $usuario_id,
            'ativo' => true
        ]);
    }
}