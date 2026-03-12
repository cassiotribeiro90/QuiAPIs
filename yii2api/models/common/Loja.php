<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\lojista\LojistaUsuario;

/**
 * This is the model class for table "lojas".
 *
 * @property int $id
 * @property string $nome
 * @property string $slug
 * @property string|null $descricao
 * @property string|null $logo
 * @property string|null $capa
 * @property string $categoria
 * @property float|null $nota_media
 * @property int $total_avaliacoes
 * @property int $tempo_entrega_min
 * @property int $tempo_entrega_max
 * @property float|null $taxa_entrega
 * @property float|null $pedido_minimo
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
 * @property bool $verificado
 * @property bool $destaque
 * @property int $trending_score
 * @property string $fluxo_status
 * @property string $cor_tema
 * @property array|null $configuracoes
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 *
 * @property Produto[] $produtos
 * @property Pedido[] $pedidos
 * @property Avaliacao[] $avaliacoes
 * @property LojaDestaque[] $lojaDestaques
 * @property LojistaLoja[] $lojistaLojas
 * @property LojistaUsuario[] $lojistas
 */
class Loja extends ActiveRecord
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;
    
    const FLUXO_BAIXO = 0;
    const FLUXO_NORMAL = 1;
    const FLUXO_ALTO = 2;
    const FLUXO_LOTADO = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lojas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'slug', 'categoria', 'tempo_entrega_min', 'tempo_entrega_max', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'telefone', 'status', 'fluxo_status'], 'required'],
            [['nome'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 100],
            [['slug'], 'unique'],
            [['descricao'], 'string'],
            [['logo', 'capa', 'instagram'], 'string', 'max' => 500],
            [['logradouro', 'cidade'], 'string', 'max' => 255],
            [['bairro'], 'string', 'max' => 100],
            [['uf'], 'string', 'max' => 2],
            [['numero'], 'string', 'max' => 20],
            [['cep'], 'string', 'max' => 9],
            [['telefone', 'whatsapp', 'email'], 'string', 'max' => 255],
            [['categoria'], 'string', 'max' => 100],
            [['nota_media'], 'number', 'min' => 0, 'max' => 5],
            [['total_avaliacoes', 'tempo_entrega_min', 'tempo_entrega_max', 'trending_score'], 'integer'],
            [['taxa_entrega', 'pedido_minimo'], 'number'],
            [['status'], 'in', 'range' => ['ativo','inativo','fechado','revisao']],
            [['verificado', 'destaque'], 'boolean'],
            [['fluxo_status'], 'in', 'range' => ['vazio','normal','cheio','lotado']],
            [['cor_tema'], 'string', 'max' => 7],
            [['configuracoes'], 'safe'],
            [['total_avaliacoes'], 'default', 'value' => 0],
            [['nota_media'], 'default', 'value' => 0],
            [['trending_score'], 'default', 'value' => 0],
            [['fluxo_status'], 'default', 'value' => self::FLUXO_NORMAL],
            [['verificado'], 'default', 'value' => false],
            [['destaque'], 'default', 'value' => false],
            [['cor_tema'], 'default', 'value' => '#FF6B6B'],
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
            'nome' => 'Nome da Loja',
            'slug' => 'Slug (URL amigável)',
            'descricao' => 'Descrição',
            'logo' => 'Logo',
            'capa' => 'Imagem de Capa',
            'categoria' => 'Categoria',
            'nota_media' => 'Nota Média',
            'total_avaliacoes' => 'Total de Avaliações',
            'tempo_entrega_min' => 'Tempo de Entrega Mínimo',
            'tempo_entrega_max' => 'Tempo de Entrega Máximo',
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
            'status' => 'Status',
            'verificado' => 'Verificado',
            'destaque' => 'Destaque',
            'trending_score' => 'Trending Score',
            'fluxo_status' => 'Status do Fluxo',
            'cor_tema' => 'Cor Tema',
            'configuracoes' => 'Configurações',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }

    /**
     * Gets query for [[Produtos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['loja_id' => 'id']);
    }

    /**
     * Gets query for [[Pedidos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPedidos()
    {
        return $this->hasMany(Pedido::class, ['loja_id' => 'id']);
    }

    /**
     * Gets query for [[Avaliacoes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAvaliacoes()
    {
        return $this->hasMany(Avaliacao::class, ['loja_id' => 'id']);
    }

    /**
     * Gets query for [[LojaDestaques]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLojaDestaques()
    {
        return $this->hasMany(LojaDestaque::class, ['loja_id' => 'id']);
    }

    /**
     * Gets query for [[LojistaLojas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLojistaLojas()
    {
        return $this->hasMany(LojistaLoja::class, ['loja_id' => 'id']);
    }

    /**
     * Gets query for [[Lojistas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLojistas()
    {
        return $this->hasMany(LojistaUsuario::class, ['id' => 'lojista_id'])
            ->viaTable('lojista_lojas', ['loja_id' => 'id']);
    }

    /**
     * Retorna lista de status de fluxo
     */
    public static function getFluxoStatusList()
    {
        return [
            self::FLUXO_BAIXO => 'Baixo',
            self::FLUXO_NORMAL => 'Normal',
            self::FLUXO_ALTO => 'Alto',
            self::FLUXO_LOTADO => 'Lotado',
        ];
    }

    /**
     * Retorna texto do status de fluxo
     */
    public function getFluxoStatusTexto()
    {
        $list = self::getFluxoStatusList();
        return $list[$this->fluxo_status] ?? 'Desconhecido';
    }

    /**
     * Retorna produtos ativos da loja
     */
    public function getProdutosAtivos()
    {
        return $this->getProdutos()->where(['ativo' => 1, 'disponivel' => 1]);
    }

    /**
     * Calcula a nota média atualizada
     */
    public function calcularNotaMedia()
    {
        $avaliacoes = $this->getAvaliacoes()->select('nota')->column();
        
        if (empty($avaliacoes)) {
            $this->nota_media = 0;
            $this->total_avaliacoes = 0;
        } else {
            $this->nota_media = array_sum($avaliacoes) / count($avaliacoes);
            $this->total_avaliacoes = count($avaliacoes);
        }
        
        return $this->save(false);
    }
}