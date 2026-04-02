<?php
// models/api/app/Produto.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class Produto
 * 
 * @property int $id
 * @property int $loja_id
 * @property int|null $subcategoria_id
 * @property string $tipo
 * @property string $nome
 * @property string|null $descricao
 * @property string|null $slug
 * @property float $preco
 * @property float|null $preco_promocional
 * @property string|null $imagem
 * @property array|null $imagens
 * @property array|null $ingredientes
 * @property string|null $ingredientes_texto
 * @property int|null $calorias
 * @property int|null $peso_gramas
 * @property int $contem_gluten
 * @property int $contem_lactose
 * @property int $vegano
 * @property int $vegetariano
 * @property int $apimentado
 * @property array|null $selos
 * @property string|null $disponivel_inicio
 * @property string|null $disponivel_fim
 * @property array|null $disponivel_dias
 * @property string|null $ultima_venda_em
 * @property int $vendas_hoje
 * @property array|null $variacoes
 * @property array|null $opcoes
 * @property int|null $tempo_preparo_min
 * @property int $disponivel
 * @property int|null $estoque
 * @property int $ordem
 * @property float $nota_media
 * @property int $total_avaliacoes
 * @property int $visualizacoes
 * @property int $cliques
 * @property int $ativo
 * @property int $destaque
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 * 
 * @property Loja $loja
 * @property Subcategoria $subcategoria
 */
class Produto extends ActiveRecord
{
    const TIPO_SIMPLES = 'simples';
    const TIPO_COMBO = 'combo';
    const TIPO_PERSONALIZAVEL = 'personalizavel';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'produto';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loja_id', 'nome', 'preco'], 'required'],
            [['loja_id', 'subcategoria_id', 'calorias', 'peso_gramas', 'tempo_preparo_min', 'estoque', 'ordem', 'total_avaliacoes', 'visualizacoes', 'cliques', 'vendas_hoje'], 'integer'],
            [['preco', 'preco_promocional', 'nota_media'], 'number'],
            [['nome', 'descricao', 'slug', 'imagem', 'ingredientes_texto'], 'string', 'max' => 255],
            [['tipo'], 'string', 'max' => 20],
            [['imagens', 'ingredientes', 'selos', 'disponivel_dias', 'variacoes', 'opcoes'], 'safe'],
            [['disponivel_inicio', 'disponivel_fim', 'ultima_venda_em', 'criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
            [['tipo'], 'in', 'range' => [self::TIPO_SIMPLES, self::TIPO_COMBO, self::TIPO_PERSONALIZAVEL]],
            [['contem_gluten', 'contem_lactose', 'vegano', 'vegetariano', 'apimentado', 'disponivel', 'ativo', 'destaque'], 'boolean'],
            [['disponivel', 'ativo', 'destaque'], 'default', 'value' => 1],
            [['ordem'], 'default', 'value' => 0],
            [['nota_media'], 'default', 'value' => 0],
            [['total_avaliacoes', 'visualizacoes', 'cliques', 'vendas_hoje'], 'default', 'value' => 0],
            [['loja_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loja::class, 'targetAttribute' => ['loja_id' => 'id']],
            [['subcategoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subcategoria::class, 'targetAttribute' => ['subcategoria_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loja_id' => 'Loja',
            'subcategoria_id' => 'Subcategoria',
            'tipo' => 'Tipo',
            'nome' => 'Nome do Produto',
            'descricao' => 'Descrição',
            'slug' => 'Slug',
            'preco' => 'Preço',
            'preco_promocional' => 'Preço Promocional',
            'imagem' => 'Imagem Principal',
            'imagens' => 'Imagens Adicionais',
            'ingredientes' => 'Ingredientes',
            'ingredientes_texto' => 'Texto dos Ingredientes',
            'calorias' => 'Calorias',
            'peso_gramas' => 'Peso (gramas)',
            'contem_gluten' => 'Contém Glúten',
            'contem_lactose' => 'Contém Lactose',
            'vegano' => 'Vegano',
            'vegetariano' => 'Vegetariano',
            'apimentado' => 'Apimentado',
            'selos' => 'Selos',
            'disponivel_inicio' => 'Disponível a partir de',
            'disponivel_fim' => 'Disponível até',
            'disponivel_dias' => 'Dias Disponíveis',
            'ultima_venda_em' => 'Última Venda',
            'vendas_hoje' => 'Vendas Hoje',
            'variacoes' => 'Variações',
            'opcoes' => 'Opções',
            'tempo_preparo_min' => 'Tempo de Preparo (min)',
            'disponivel' => 'Disponível',
            'estoque' => 'Estoque',
            'ordem' => 'Ordem',
            'nota_media' => 'Nota Média',
            'total_avaliacoes' => 'Total Avaliações',
            'visualizacoes' => 'Visualizações',
            'cliques' => 'Cliques',
            'ativo' => 'Ativo',
            'destaque' => 'Destaque',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }
    
    /**
     * Gets query for [[Loja]].
     */
    public function getLoja()
    {
        return $this->hasOne(Loja::class, ['id' => 'loja_id']);
    }
    
    /**
     * Gets query for [[Subcategoria]].
     */
    public function getSubcategoria()
    {
        return $this->hasOne(Subcategoria::class, ['id' => 'subcategoria_id']);
    }
    
    /**
     * Retorna o preço atual (promocional ou normal)
     */
    public function getPrecoAtual()
    {
        return $this->preco_promocional ?: $this->preco;
    }
    
    /**
     * Formata o preço para exibição
     */
    public function getPrecoFormatado()
    {
        return 'R$ ' . number_format($this->getPrecoAtual(), 2, ',', '.');
    }
    
    /**
     * Verifica se o produto está disponível no momento
     */
    public function isDisponivelAgora()
    {
        if (!$this->disponivel || !$this->ativo) {
            return false;
        }
        
        // Verifica horário de funcionamento
        if ($this->disponivel_inicio && $this->disponivel_fim) {
            $agora = date('H:i:s');
            if ($agora < $this->disponivel_inicio || $agora > $this->disponivel_fim) {
                return false;
            }
        }
        
        // Verifica dias da semana
        if ($this->disponivel_dias) {
            $dias = is_array($this->disponivel_dias) ? $this->disponivel_dias : json_decode($this->disponivel_dias, true);
            $hoje = date('N'); // 1=Segunda, 7=Domingo
            if (!in_array($hoje, $dias)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Retorna o status do estoque
     */
    public function getStatusEstoque()
    {
        if ($this->estoque === null) {
            return 'ilimitado';
        }
        if ($this->estoque <= 0) {
            return 'esgotado';
        }
        if ($this->estoque <= 5) {
            return 'baixo';
        }
        return 'normal';
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Converte arrays para JSON antes de salvar
            $jsonFields = ['imagens', 'ingredientes', 'selos', 'disponivel_dias', 'variacoes', 'opcoes'];
            foreach ($jsonFields as $field) {
                if (is_array($this->$field)) {
                    $this->$field = json_encode($this->$field, JSON_UNESCAPED_UNICODE);
                }
            }
            return true;
        }
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        
        // Converte JSON de volta para array após buscar
        $jsonFields = ['imagens', 'ingredientes', 'selos', 'disponivel_dias', 'variacoes', 'opcoes'];
        foreach ($jsonFields as $field) {
            if ($this->$field && is_string($this->$field)) {
                $decoded = json_decode($this->$field, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->$field = $decoded;
                }
            }
        }
    }
}