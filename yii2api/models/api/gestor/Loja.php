<?php
// models/api/gestor/Loja.php

namespace app\models\api\gestor;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "loja".
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
 * @property string $status  // ativo, inativo, fechado, revisao
 * @property int $verificado
 * @property int $destaque
 * @property int $trending_score
 * @property string $fluxo_status  // vazio, normal, cheio, lotado
 * @property string $cor_tema
 * @property array|null $configuracoes
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 */
class Loja extends ActiveRecord
{
    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    const STATUS_FECHADO = 'fechado';
    const STATUS_REVISAO = 'revisao';

    const FLUXO_VAZIO = 'vazio';
    const FLUXO_NORMAL = 'normal';
    const FLUXO_CHEIO = 'cheio';
    const FLUXO_LOTADO = 'lotado';

    public static function tableName()
    {
        return '{{%loja}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'nome',
                'ensureUnique' => true,
                'immutable' => true,
            ],
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'criado_em',
                'updatedAtAttribute' => 'atualizado_em',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['nome', 'categoria', 'tempo_entrega_min', 'tempo_entrega_max', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'telefone'], 'required'],
            [['descricao', 'configuracoes'], 'safe'],
            [['nota_media', 'taxa_entrega', 'pedido_minimo', 'latitude', 'longitude'], 'number'],
            [['total_avaliacoes', 'tempo_entrega_min', 'tempo_entrega_max', 'verificado', 'destaque', 'trending_score'], 'integer'],
            [['criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
            [['status', 'fluxo_status'], 'string'],
            [['nome', 'slug', 'categoria', 'logradouro', 'bairro', 'cidade', 'instagram'], 'string', 'max' => 255],
            [['logo', 'capa'], 'string', 'max' => 500],
            [['cep', 'numero', 'uf'], 'string', 'max' => 20],
            [['complemento', 'email'], 'string', 'max' => 255],
            [['telefone', 'whatsapp'], 'string', 'max' => 20],
            [['cor_tema'], 'string', 'max' => 7],
            [['slug'], 'unique'],
            [['email'], 'email'],
            ['status', 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_INATIVO, self::STATUS_FECHADO, self::STATUS_REVISAO]],
            ['fluxo_status', 'in', 'range' => [self::FLUXO_VAZIO, self::FLUXO_NORMAL, self::FLUXO_CHEIO, self::FLUXO_LOTADO]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'descricao' => 'Descrição',
            'slug' => 'Slug',
            'categoria' => 'Categoria',
            'logo' => 'Logo',
            'capa' => 'Capa',
            'nota_media' => 'Nota Média',
            'total_avaliacoes' => 'Total Avaliações',
            'tempo_entrega_min' => 'Tempo Entrega Mín (min)',
            'tempo_entrega_max' => 'Tempo Entrega Máx (min)',
            'taxa_entrega' => 'Taxa Entrega',
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
            'fluxo_status' => 'Fluxo Status',
            'cor_tema' => 'Cor Tema',
            'configuracoes' => 'Configurações',
            'criado_em' => 'Criado Em',
            'atualizado_em' => 'Atualizado Em',
            'deletado_em' => 'Deletado Em',
        ];
    }

    public static function find()
    {
        return parent::find()->andWhere(['deletado_em' => null]);
    }

    public function softDelete()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    public function isAtivo()
    {
        return $this->status === self::STATUS_ATIVO && $this->deletado_em === null;
    }

    // Relacionamentos (opcionais)
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['loja_id' => 'id']);
    }

    public function getLojistas()
    {
        return $this->hasMany(StoreUsuario::class, ['id' => 'usuario_id'])
            ->viaTable('store_usuario_loja', ['loja_id' => 'id']);
    }
}