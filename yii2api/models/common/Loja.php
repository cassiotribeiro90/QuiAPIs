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
 * @property string|null $endereco
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $telefone
 * @property string|null $whatsapp
 * @property string|null $email
 * @property string|null $site
 * @property string|null $instagram
 * @property string|null $facebook
 * @property string $categoria
 * @property float|null $nota_media
 * @property int $total_avaliacoes
 * @property float|null $trending_score
 * @property int $fluxo_status
 * @property int $ativo
 * @property int $destaque
 * @property int $ordem
 * @property string $created_at
 * @property string $updated_at
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
            [['nome', 'slug', 'categoria'], 'required'],
            [['nome'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 100],
            [['slug'], 'unique'],
            [['descricao'], 'string'],
            [['logo', 'capa', 'endereco', 'site', 'instagram', 'facebook'], 'string', 'max' => 500],
            [['latitude', 'longitude'], 'string', 'max' => 20],
            [['telefone', 'whatsapp', 'email'], 'string', 'max' => 100],
            [['categoria'], 'string', 'max' => 50],
            [['nota_media', 'trending_score'], 'number', 'min' => 0, 'max' => 5],
            [['total_avaliacoes', 'fluxo_status', 'ativo', 'destaque', 'ordem'], 'integer'],
            [['total_avaliacoes'], 'default', 'value' => 0],
            [['nota_media'], 'default', 'value' => 0],
            [['fluxo_status'], 'default', 'value' => self::FLUXO_NORMAL],
            [['ativo'], 'default', 'value' => self::STATUS_ATIVO],
            [['destaque'], 'default', 'value' => 0],
            [['ordem'], 'default', 'value' => 0],
            [['created_at', 'updated_at'], 'safe'],
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
            'endereco' => 'Endereço Completo',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'telefone' => 'Telefone',
            'whatsapp' => 'WhatsApp',
            'email' => 'E-mail',
            'site' => 'Site',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'categoria' => 'Categoria',
            'nota_media' => 'Nota Média',
            'total_avaliacoes' => 'Total de Avaliações',
            'trending_score' => 'Trending Score',
            'fluxo_status' => 'Status do Fluxo',
            'ativo' => 'Ativo',
            'destaque' => 'Destaque',
            'ordem' => 'Ordem',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
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