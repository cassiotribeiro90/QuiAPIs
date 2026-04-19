<?php
// models/api/app/Loja.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class Loja
 * 
 * @property int $id
 * @property string $nome
 * @property string|null $descricao
 * @property string $slug
 * @property string $categoria
 * @property string|null $logo
 * @property string|null $capa
 * @property float $nota_media
 * @property int $total_avaliacoes
 * @property int $tempo_entrega_min
 * @property int $tempo_entrega_max
 * @property float $taxa_entrega
 * @property float $pedido_minimo
 * @property string $cep
 * @property string $logradouro
 * @property string $numero
 * @property string|null $complemento
 * @property string $bairro
 * @property string $cidade
 * @property string $uf
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string $telefone
 * @property string|null $whatsapp
 * @property string|null $email
 * @property string|null $instagram
 * @property string $status
 * @property int $verificado
 * @property int $destaque
 * @property int $trending_score
 * @property string $fluxo_status
 * @property string $cor_tema
 * @property array|null $configuracoes
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 * 
 * @property Produto[] $produtos
 */
class Loja extends ActiveRecord
{
    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    const STATUS_FECHADO = 'fechado';
    const STATUS_REVISAO = 'revisao';
    
    const FLUXO_STATUS_VAZIO = 'vazio';
    const FLUXO_STATUS_NORMAL = 'normal';
    const FLUXO_STATUS_CHEIO = 'cheio';
    const FLUXO_STATUS_LOTADO = 'lotado';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loja';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'slug', 'categoria', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'telefone'], 'required'],
            [['nota_media', 'taxa_entrega', 'pedido_minimo', 'latitude', 'longitude'], 'number'],
            [['total_avaliacoes', 'tempo_entrega_min', 'tempo_entrega_max', 'trending_score', 'verificado', 'destaque'], 'integer'],
            [['descricao', 'slug', 'logo', 'capa', 'logradouro', 'complemento', 'bairro', 'cidade', 'uf', 'telefone', 'whatsapp', 'email', 'instagram', 'status', 'fluxo_status', 'cor_tema', 'configuracoes'], 'string'],
            [['criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
            [['slug'], 'unique'],
            [['ativo'], 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_INATIVO, self::STATUS_FECHADO, self::STATUS_REVISAO]],
            [['fluxo_status'], 'in', 'range' => [self::FLUXO_STATUS_VAZIO, self::FLUXO_STATUS_NORMAL, self::FLUXO_STATUS_CHEIO, self::FLUXO_STATUS_LOTADO]],
            [['destaque', 'verificado'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_REVISAO],
            [['fluxo_status'], 'default', 'value' => self::FLUXO_STATUS_NORMAL],
            [['cor_tema'], 'default', 'value' => '#FF6B6B'],
            [['configuracoes'], 'default', 'value' => null],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome da Loja',
            'descricao' => 'Descrição',
            'slug' => 'Slug',
            'categoria' => 'Categoria',
            'logo' => 'Logo',
            'capa' => 'Capa',
            'nota_media' => 'Nota Média',
            'total_avaliacoes' => 'Total Avaliações',
            'tempo_entrega_min' => 'Tempo Mínimo Entrega',
            'tempo_entrega_max' => 'Tempo Máximo Entrega',
            'taxa_entrega' => 'Taxa de Entrega',
            'pedido_minimo' => 'Pedido Mínimo',
            'cep' => 'CEP',
            'logradouro' => 'Logradouro',
            'numero' => 'Número',
            'complemento' => 'Complemento',
            'bairro' => 'Bairro',
            'cidade' => 'Cidade',
            'uf' => 'UF',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'telefone' => 'Telefone',
            'whatsapp' => 'WhatsApp',
            'email' => 'E-mail',
            'instagram' => 'Instagram',
            'ativo' => '1',
            'verificado' => 'Verificado',
            'destaque' => 'Destaque',
            'trending_score' => 'Trending Score',
            'fluxo_status' => 'Fluxo de Pedidos',
            'cor_tema' => 'Cor Tema',
            'configuracoes' => 'Configurações',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }
    
    /**
     * Gets query for [[Produtos]].
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['loja_id' => 'id']);
    }
    
    /**
     * Retorna o endereço completo formatado
     */
    public function getEnderecoCompleto()
    {
        $partes = array_filter([
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->bairro,
            $this->cidade,
            $this->uf,
            $this->cep
        ]);
        
        return implode(', ', $partes);
    }
    
    /**
     * Retorna o endereço resumido (logradouro + número)
     */
    public function getEnderecoResumido()
    {
        $partes = array_filter([
            $this->logradouro,
            $this->numero
        ]);
        
        return implode(', ', $partes);
    }
    
    /**
     * Retorna se a loja está aberta (status ativo)
     */
    public function isAberto()
    {
        return $this->status === self::STATUS_ATIVO;
    }
    
    /**
     * Retorna se a loja aceita pedidos no momento
     */
    public function podeReceberPedidos()
    {
        return $this->status === self::STATUS_ATIVO && 
               $this->fluxo_status !== self::FLUXO_STATUS_LOTADO;
    }
}