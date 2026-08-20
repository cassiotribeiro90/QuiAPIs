<?php
// models/api/app/Pedido.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pedido".
 *
 * @property int $id
 * @property string $codigo
 * @property int $usuario_id
 * @property int $loja_id
 * @property int|null $endereco_id
 * @property string $status
 * @property array|null $status_historico
 * @property string $data_pedido
 * @property string|null $data_confirmacao
 * @property string|null $data_preparo
 * @property string|null $data_saida
 * @property string|null $data_entrega
 * @property string|null $data_cancelamento
 * @property string|null $observacoes
 * @property float $subtotal
 * @property float $taxa_entrega
 * @property float $desconto
 * @property float $total
 * @property string $forma_pagamento
 * @property string $pagamento_status
 * @property float|null $troco_para
 * @property array|null $pagamento_detalhes
 * @property array|null $pagamento_config
 * @property array|null $endereco_entrega
 * @property int|null $tempo_espera_min
 * @property float|null $distancia_km
 * @property int|null $tempo_real_min
 * @property float|null $entregador_lat
 * @property float|null $entregador_lng
 * @property string|null $entregador_atualizado_em
 * @property string|null $cancelado_por
 * @property string|null $cancelado_motivo
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 */
class Pedido extends ActiveRecord
{
    // ==================== STATUS DO PEDIDO ====================
    const STATUS_NOVO = 'novo';
    const STATUS_AGUARDANDO = 'aguardando';
    const STATUS_CONFIRMADO = 'confirmado';
    const STATUS_PREPARANDO = 'preparando';
    const STATUS_PRONTO = 'pronto';
    const STATUS_SAIU = 'saiu';
    const STATUS_ENTREGUE = 'entregue';
    const STATUS_CANCELADO = 'cancelado';
    const STATUS_RECUSADO = 'recusado'; // ✅ NOVO

    // ==================== STATUS DE PAGAMENTO ====================
    const PAGAMENTO_PENDENTE = 'pendente';
    const PAGAMENTO_APROVADO = 'aprovado';
    const PAGAMENTO_RECUSADO = 'recusado';
    const PAGAMENTO_CANCELADO = 'cancelado';
    const PAGAMENTO_ESTORNADO = 'estornado'; // ✅ NOVO
    const PAGAMENTO_EM_ANALISE = 'em_analise'; // ✅ NOVO

    // ==================== FORMAS DE PAGAMENTO ====================
    const PAGAMENTO_CREDITO = 'credito';
    const PAGAMENTO_DEBITO = 'debito';
    const PAGAMENTO_DINHEIRO = 'dinheiro';
    const PAGAMENTO_PIX = 'pix';
    const PAGAMENTO_VR = 'vr';
    const PAGAMENTO_CARTAO_ENTREGA = 'cartao_entrega'; // ✅ NOVO
    const PAGAMENTO_CARTAO_CREDITO = 'cartao_credito'; // ✅ NOVO
    const PAGAMENTO_CARTAO_DEBITO = 'cartao_debito'; // ✅ NOVO
    const PAGAMENTO_VALE_REFEICAO = 'vale_refeicao'; // ✅ NOVO
    const PAGAMENTO_VALE_ALIMENTACAO = 'vale_alimentacao'; // ✅ NOVO
    const PAGAMENTO_TRANSFERENCIA = 'transferencia'; // ✅ NOVO
    const PAGAMENTO_BOLETO = 'boleto'; // ✅ NOVO
    const PAGAMENTO_OUTRO = 'outro'; // ✅ NOVO

    public static function tableName()
    {
        return '{{%pedido}}';
    }

    public function rules()
    {
        return [
            // Campos obrigatórios
            [['codigo', 'usuario_id', 'loja_id', 'subtotal', 'total', 'forma_pagamento'], 'required'],

            // Inteiros
            [['usuario_id', 'loja_id', 'endereco_id', 'tempo_espera_min', 'tempo_real_min'], 'integer'],

            // Decimais
            [['subtotal', 'taxa_entrega', 'desconto', 'total', 'troco_para', 'distancia_km', 'entregador_lat', 'entregador_lng'], 'number'],

            // JSON / arrays
            [['status_historico', 'pagamento_detalhes', 'pagamento_config', 'endereco_entrega'], 'safe'], // ✅ pagamento_config

            // Textos longos
            [['observacoes', 'cancelado_motivo'], 'string'],

            // Datas
            [['data_pedido', 'data_confirmacao', 'data_preparo', 'data_saida', 'data_entrega',
              'data_cancelamento', 'entregador_atualizado_em', 'criado_em',
              'atualizado_em', 'deletado_em'], 'safe'],

            // Strings limitadas
            [['codigo'], 'string', 'max' => 50],
            [['status', 'forma_pagamento', 'pagamento_status', 'cancelado_por'], 'string'],
            [['codigo'], 'unique'],

            // ✅ Validações de enum expandidas
            ['status', 'in', 'range' => [
                self::STATUS_NOVO, self::STATUS_AGUARDANDO, self::STATUS_CONFIRMADO,
                self::STATUS_PREPARANDO, self::STATUS_PRONTO, self::STATUS_SAIU,
                self::STATUS_ENTREGUE, self::STATUS_CANCELADO, self::STATUS_RECUSADO,
            ]],
            ['forma_pagamento', 'in', 'range' => [
                self::PAGAMENTO_CREDITO, self::PAGAMENTO_DEBITO, self::PAGAMENTO_DINHEIRO,
                self::PAGAMENTO_PIX, self::PAGAMENTO_VR,
                self::PAGAMENTO_CARTAO_ENTREGA, self::PAGAMENTO_CARTAO_CREDITO,
                self::PAGAMENTO_CARTAO_DEBITO, self::PAGAMENTO_VALE_REFEICAO,
                self::PAGAMENTO_VALE_ALIMENTACAO, self::PAGAMENTO_TRANSFERENCIA,
                self::PAGAMENTO_BOLETO, self::PAGAMENTO_OUTRO,
            ]],
            ['pagamento_status', 'in', 'range' => [
                self::PAGAMENTO_PENDENTE, self::PAGAMENTO_APROVADO,
                self::PAGAMENTO_RECUSADO, self::PAGAMENTO_CANCELADO,
                self::PAGAMENTO_ESTORNADO, self::PAGAMENTO_EM_ANALISE,
            ]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código',
            'usuario_id' => 'Usuário',
            'loja_id' => 'Loja',
            'endereco_id' => 'Endereço',
            'status' => 'Status',
            'status_historico' => 'Histórico de Status',
            'data_pedido' => 'Data do Pedido',
            'data_confirmacao' => 'Data Confirmação',
            'data_preparo' => 'Data Preparo',
            'data_saida' => 'Data Saída',
            'data_entrega' => 'Data Entrega',
            'data_cancelamento' => 'Data Cancelamento',
            'observacoes' => 'Observações',
            'subtotal' => 'Subtotal',
            'taxa_entrega' => 'Taxa Entrega',
            'desconto' => 'Desconto',
            'total' => 'Total',
            'forma_pagamento' => 'Forma de Pagamento',
            'pagamento_status' => 'Status do Pagamento',
            'troco_para' => 'Troco para',
            'pagamento_detalhes' => 'Detalhes do Pagamento',
            'pagamento_config' => 'Configuração do Pagamento',
            'endereco_entrega' => 'Endereço de Entrega',
            'tempo_espera_min' => 'Tempo Espera (min)',
            'distancia_km' => 'Distância (km)',
            'tempo_real_min' => 'Tempo Real (min)',
            'entregador_lat' => 'Latitude do Entregador',
            'entregador_lng' => 'Longitude do Entregador',
            'entregador_atualizado_em' => 'Última Atualização do Entregador',
            'cancelado_por' => 'Cancelado Por',
            'cancelado_motivo' => 'Motivo do Cancelamento',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }

    // ===== RELACIONAMENTOS =====

    public function getUsuario()
    {
        return $this->hasOne(Usuario::class, ['id' => 'usuario_id']);
    }

    public function getLoja()
    {
        return $this->hasOne(\app\models\api\gestor\Loja::class, ['id' => 'loja_id']);
    }

    public function getEndereco()
    {
        return $this->hasOne(AppEndereco::class, ['id' => 'endereco_id']);
    }

    public function getItens()
    {
        return $this->hasMany(PedidoItem::class, ['pedido_id' => 'id']);
    }

    public function getAvaliacao()
    {
        return $this->hasOne(Avaliacao::class, ['pedido_id' => 'id']);
    }

    // ===== MÉTODOS DE STATUS =====

    public function isNovo()
    {
        return $this->status == self::STATUS_NOVO;
    }

    public function isConfirmado()
    {
        return $this->status == self::STATUS_CONFIRMADO;
    }

    public function isPreparando()
    {
        return $this->status == self::STATUS_PREPARANDO;
    }

    public function isPronto()
    {
        return $this->status == self::STATUS_PRONTO;
    }

    public function isSaiu()
    {
        return $this->status == self::STATUS_SAIU;
    }

    public function isEntregue()
    {
        return $this->status == self::STATUS_ENTREGUE;
    }

    public function isCancelado()
    {
        return $this->status == self::STATUS_CANCELADO;
    }

    public function isRecusado()
    {
        return $this->status == self::STATUS_RECUSADO;
    }

    public function isPagamentoAprovado()
    {
        return $this->pagamento_status == self::PAGAMENTO_APROVADO;
    }

    // ===== MÉTODOS DE MUDANÇA DE STATUS =====

    public function alterarStatus($novoStatus, $motivo = null)
    {
        $statusAnterior = $this->status;
        $this->status = $novoStatus;

        $historico = $this->status_historico ?: [];
        $historico[] = [
            'status' => $novoStatus,
            'data' => date('Y-m-d H:i:s'),
            'motivo' => $motivo,
            'anterior' => $statusAnterior,
        ];
        $this->status_historico = $historico;

        switch ($novoStatus) {
            case self::STATUS_CONFIRMADO:
                $this->data_confirmacao = date('Y-m-d H:i:s');
                break;
            case self::STATUS_PREPARANDO:
                $this->data_preparo = date('Y-m-d H:i:s');
                break;
            case self::STATUS_SAIU:
                $this->data_saida = date('Y-m-d H:i:s');
                break;
            case self::STATUS_ENTREGUE:
                $this->data_entrega = date('Y-m-d H:i:s');
                break;
            case self::STATUS_CANCELADO:
                $this->data_cancelamento = date('Y-m-d H:i:s');
                break;
            case self::STATUS_RECUSADO:
                $this->data_cancelamento = date('Y-m-d H:i:s');
                break;
        }

        return $this->save(false);
    }

    // ===== MÉTODOS DE PAGAMENTO =====

    public function aprovarPagamento()
    {
        $this->pagamento_status = self::PAGAMENTO_APROVADO;
        return $this->save(false);
    }

    public function recusarPagamento($motivo)
    {
        $this->pagamento_status = self::PAGAMENTO_RECUSADO;
        $this->cancelado_motivo = $motivo;
        return $this->save(false);
    }

    // ===== MÉTODOS DE CÁLCULO =====

    public function calcularTotal()
    {
        $this->total = $this->subtotal + $this->taxa_entrega - ($this->desconto ?? 0);
        return $this->total;
    }

    // ===== GERAR CÓDIGO =====

    public function gerarCodigo()
    {
        $prefixo = date('Ymd');
        $this->codigo = $prefixo . '-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
        return $this->codigo;
    }

    // ===== MÉTODOS ESTÁTICOS =====

    public static function getStatusOptions()
    {
        return [
            self::STATUS_NOVO => 'Novo',
            self::STATUS_AGUARDANDO => 'Aguardando',
            self::STATUS_CONFIRMADO => 'Confirmado',
            self::STATUS_PREPARANDO => 'Preparando',
            self::STATUS_PRONTO => 'Pronto',
            self::STATUS_SAIU => 'Saiu para Entrega',
            self::STATUS_ENTREGUE => 'Entregue',
            self::STATUS_CANCELADO => 'Cancelado',
            self::STATUS_RECUSADO => 'Recusado',
        ];
    }

    public static function getPagamentoOptions()
    {
        return [
            self::PAGAMENTO_CREDITO => 'Cartão de Crédito',
            self::PAGAMENTO_DEBITO => 'Cartão de Débito',
            self::PAGAMENTO_DINHEIRO => 'Dinheiro',
            self::PAGAMENTO_PIX => 'PIX',
            self::PAGAMENTO_VR => 'Vale Refeição',
            self::PAGAMENTO_CARTAO_ENTREGA => 'Cartão na Entrega',
            self::PAGAMENTO_CARTAO_CREDITO => 'Cartão de Crédito (Online)',
            self::PAGAMENTO_CARTAO_DEBITO => 'Cartão de Débito (Online)',
            self::PAGAMENTO_VALE_REFEICAO => 'Vale Refeição',
            self::PAGAMENTO_VALE_ALIMENTACAO => 'Vale Alimentação',
            self::PAGAMENTO_TRANSFERENCIA => 'Transferência',
            self::PAGAMENTO_BOLETO => 'Boleto',
            self::PAGAMENTO_OUTRO => 'Outro',
        ];
    }
}