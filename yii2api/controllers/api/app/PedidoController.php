<?php
// controllers/api/app/PedidoController.php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\components\FirebaseService;
use app\models\api\app\Pedido;
use app\models\api\app\PedidoItem;
use app\models\api\app\Carrinho;
use app\models\api\app\CarrinhoItem;
use app\models\api\app\Loja;
use app\models\api\app\AppEndereco;
use app\controllers\api\app\AppControllerBase;
use app\models\api\app\Usuario;
use app\models\api\lojista\LojistaUsuario;
use app\models\api\lojista\LojistaUsuarioLoja;

class PedidoController extends AppControllerBase
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    /**
     * POST /api/app/pedido/calcular-frete
     */
    public function actionCalcularFrete()
    {
        $request = Yii::$app->request;
        $usuarioId = Yii::$app->user->id;

        $lojaId = $request->post('loja_id');
        $enderecoId = $request->post('endereco_id');

        if (!$lojaId || !$enderecoId) {
            return ApiResponse::error('loja_id e endereco_id são obrigatórios', 400);
        }

        $loja = Loja::findOne($lojaId);
        if (!$loja) {
            return ApiResponse::error('Loja não encontrada', 404);
        }

        $endereco = AppEndereco::find()
            ->where(['id' => $enderecoId, 'usuario_id' => $usuarioId])
            ->one();
        if (!$endereco) {
            return ApiResponse::error('Endereço não encontrado', 404);
        }

        $distancia = $this->calcularDistancia(
            $loja->latitude, $loja->longitude,
            $endereco->latitude, $endereco->longitude
        );

        $taxa = $this->calcularTaxaEntrega($distancia);

        return ApiResponse::success([
            'distancia_km' => round($distancia, 2),
            'taxa_entrega' => $taxa,
            'taxa_entrega_formatada' => 'R$ ' . number_format($taxa, 2, ',', '.'),
        ]);
    }

    function calcularTaxaEntrega($distancia)
    {
        if ($distancia <= 5) {
            return 10.00;
        } elseif ($distancia <= 10) {
            return 15.00;
        } else {
            return 20.00;
        }
    }

    /**
     * POST /api/app/pedido/criar
     */
    public function actionCriar()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $request = Yii::$app->request;
            $usuarioId = Yii::$app->user->id;

            // 1. Carrinho ativo
            $carrinho = Carrinho::find()
                ->where(['usuario_id' => $usuarioId, 'status' => 'ativo'])
                ->one();
            if (!$carrinho) {
                return ApiResponse::error('Carrinho vazio', 400);
            }

            $itensCarrinho = CarrinhoItem::find()
                ->where(['carrinho_id' => $carrinho->id])
                ->all();
            if (empty($itensCarrinho)) {
                return ApiResponse::error('Carrinho vazio', 400);
            }

            $enderecoId = $request->post('endereco_id');
            if (empty($enderecoId)) {
                return ApiResponse::error('ID do endereço é obrigatório', 400);
            }
            $endereco = AppEndereco::find()
                ->where(['id' => $enderecoId, 'usuario_id' => $usuarioId])
                ->one();
            if (!$endereco) {
                return ApiResponse::error('Endereço não encontrado', 400);
            }

            // 3. Forma de pagamento
            $formaPagamento = $request->post('forma_pagamento');
            if (empty($formaPagamento)) {
                return ApiResponse::error('Forma de pagamento é obrigatória', 400);
            }

            $loja = Loja::findOne($carrinho->loja_id);
            if (!$loja) {
                return ApiResponse::error('Loja não encontrada', 404);
            }

            // ✅ Todas as formas de pagamento válidas
            $formasValidas = [
                'dinheiro',
                'credito',
                'debito',
                'pix',
                'vr',
                'cartao_entrega',
                'cartao_credito',
                'cartao_debito',
                'vale_refeicao',
                'vale_alimentacao',
                'transferencia',
                'boleto',
                'outro',
            ];

            $configLoja = $loja->configuracoes;
            if (is_string($configLoja)) {
                $configLoja = json_decode($configLoja, true);
            }
            if (is_array($configLoja) && isset($configLoja['formas_pagamento'])) {
                $formasValidas = array_keys($configLoja['formas_pagamento']);
            }

            if (!in_array($formaPagamento, $formasValidas)) {
                return ApiResponse::error(
                    'Forma de pagamento não disponível para esta loja',
                    400
                );
            }

            $distancia = $this->calcularDistancia(
                $loja->latitude,
                $loja->longitude,
                $endereco->latitude,
                $endereco->longitude
            );
            $taxaEntrega = $this->calcularTaxaEntrega($distancia);

            $pedido = new Pedido();
            $pedido->usuario_id    = $usuarioId;
            $pedido->loja_id       = $carrinho->loja_id;
            $pedido->endereco_id   = $enderecoId;
            $pedido->subtotal      = $carrinho->subtotal;
            $pedido->taxa_entrega  = $taxaEntrega;
            $pedido->total         = $carrinho->subtotal + $taxaEntrega;
            $pedido->forma_pagamento = $formaPagamento;

            // ✅ Código amigável
            $pedido->codigo = $this->gerarCodigoPedido();

            $pedido->status        = Pedido::STATUS_NOVO;
            $pedido->pagamento_status = Pedido::PAGAMENTO_PENDENTE;
            $pedido->observacoes   = $request->post('observacao');

            // ✅ Configuração de pagamento (JSON)
            $pagamentoConfig = $request->post('pagamento_config');
            if (!empty($pagamentoConfig)) {
                $pedido->pagamento_config = $pagamentoConfig;
            }

            if ($formaPagamento === 'dinheiro') {
                $pedido->troco_para = $request->post('troco_para');
            }

            if (!$pedido->save()) {
                $errors = json_encode($pedido->errors);
                Yii::error("Erro ao salvar pedido: $errors", __METHOD__);
                throw new \Exception('Erro ao salvar pedido: ' . $errors);
            }

            foreach ($itensCarrinho as $item) {
                $pedidoItem = new PedidoItem();
                $pedidoItem->pedido_id      = $pedido->id;
                $pedidoItem->produto_id     = $item->produto_id;
                $pedidoItem->produto_nome   = $item->produto_nome;
                $pedidoItem->quantidade     = $item->quantidade;
                $pedidoItem->preco_unitario = $item->preco_unitario;
                $pedidoItem->preco_total    = $item->preco_total;
                $pedidoItem->observacao     = $item->observacao;
                $pedidoItem->opcoes         = $item->opcoes;
                if (!$pedidoItem->save()) {
                    $errors = json_encode($pedidoItem->errors);
                    Yii::error("Erro ao salvar item do pedido: $errors", __METHOD__);
                    throw new \Exception('Erro ao salvar item do pedido: ' . $errors);
                }
            }

            $carrinho->delete();

            $transaction->commit();

            // ============================================================
            // 🔥 ENVIA NOTIFICAÇÃO PUSH PARA OS LOJISTAS DA LOJA
            // ============================================================
            $this->enviarPushLojistas($pedido);

            return ApiResponse::success([
                'pedido_id'       => $pedido->id,
                'codigo'          => $pedido->codigo,
                'total'           => $pedido->total,
                'status'          => $pedido->status,
                'forma_pagamento' => $pedido->forma_pagamento,
                'criado_em'       => $pedido->criado_em,
            ], 'Pedido criado com sucesso', 201);

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Erro ao criar pedido: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao processar pedido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/app/pedido/historico
     */
    public function actionHistorico()
    {
        $token = Yii::$app->request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);

        $usuario = Usuario::findIdentityByAccessToken($token);
        if (!$usuario) {
            return ApiResponse::error('Usuário não autenticado', 401);
        }
        $usuarioId = $usuario->id;

        $page = (int)Yii::$app->request->get('page', 1);
        $perPage = (int)Yii::$app->request->get('per_page', 10);
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
                p.id,
                p.codigo,
                p.total,
                p.status,
                p.forma_pagamento,
                p.criado_em,
                l.nome AS loja_nome,
                l.logo AS loja_logo,
                COUNT(pi.id) AS item_count
            FROM pedido p
            LEFT JOIN loja l ON p.loja_id = l.id
            LEFT JOIN pedido_item pi ON p.id = pi.pedido_id
            WHERE p.usuario_id = :usuario_id
            GROUP BY p.id, p.codigo, p.total, p.status, p.forma_pagamento, p.criado_em, l.nome, l.logo
            ORDER BY p.criado_em DESC
            LIMIT :limit OFFSET :offset
        ";

        $command = Yii::$app->db->createCommand($sql);
        $command->bindValue(':usuario_id', $usuarioId);
        $command->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $command->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $pedidos = $command->queryAll();

        $countSql = "SELECT COUNT(*) FROM pedido WHERE usuario_id = :usuario_id";
        $total = Yii::$app->db->createCommand($countSql, [':usuario_id' => $usuarioId])->queryScalar();

        $data = array_map(function($pedido) {
            return [
                'id'                => (int)$pedido['id'],
                'codigo'            => $pedido['codigo'],
                'loja_nome'         => $pedido['loja_nome'],
                'loja_logo'         => $pedido['loja_logo'],
                'total'             => (float)$pedido['total'],
                'status'            => $pedido['status'],
                'forma_pagamento'   => $pedido['forma_pagamento'],
                'item_count'        => (int)$pedido['item_count'],
                'criado_em'         => $pedido['criado_em'],
            ];
        }, $pedidos);

        return ApiResponse::success([
            'items'      => $data,
            'pagination' => [
                'total'        => (int)$total,
                'page'         => $page,
                'per_page'     => $perPage,
                'total_pages'  => ceil($total / $perPage),
            ],
        ]);
    }

    /**
     * GET /api/app/pedido/view?id=7
     */
    public function actionView($id = null)
    {
        if ($id === null) {
            $id = Yii::$app->request->get('pedido_id');
        }

        $token = Yii::$app->request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);

        $usuario = Usuario::findIdentityByAccessToken($token);
        if (!$usuario) {
            return ApiResponse::error('Usuário não autenticado', 401);
        }
        $usuarioId = $usuario->id;

        $pedido = Pedido::findOne($id);

        if (!$pedido) {
            return ApiResponse::error('Pedido não encontrado (ID inexistente)', 404);
        }

        if ($pedido->usuario_id != $usuarioId) {
            return ApiResponse::error('Pedido não encontrado (não pertence a este usuário)', 404);
        }

        $itens = PedidoItem::find()
            ->where(['pedido_id' => $pedido->id])
            ->all();

        $endereco = $pedido->endereco;
        $loja = $pedido->loja;

        $formatarData = function($data) {
            if ($data instanceof \DateTime) {
                return $data->format('Y-m-d H:i:s');
            }
            return $data;
        };

        return ApiResponse::success([
            'id' => $pedido->id,
            'codigo' => $pedido->codigo,
            'status' => $pedido->status,
            'status_historico' => $pedido->status_historico,
            'data_pedido' => $formatarData($pedido->data_pedido),
            'data_confirmacao' => $formatarData($pedido->data_confirmacao),
            'data_preparo' => $formatarData($pedido->data_preparo),
            'data_saida' => $formatarData($pedido->data_saida),
            'data_entrega' => $formatarData($pedido->data_entrega),
            'data_cancelamento' => $formatarData($pedido->data_cancelamento),
            'loja' => $loja ? [
                'id' => $loja->id,
                'nome' => $loja->nome,
            ] : null,
            'endereco' => $endereco ? [
                'logradouro' => $endereco->logradouro,
                'numero' => $endereco->numero,
                'complemento' => $endereco->complemento,
                'bairro' => $endereco->bairro,
                'cidade' => $endereco->cidade,
                'uf' => $endereco->uf,
            ] : null,
            'subtotal' => (float)$pedido->subtotal,
            'taxa_entrega' => (float)$pedido->taxa_entrega,
            'desconto' => (float)$pedido->desconto,
            'total' => (float)$pedido->total,
            'forma_pagamento' => $pedido->forma_pagamento,
            'pagamento_status' => $pedido->pagamento_status,
            'troco_para' => $pedido->troco_para ? (float)$pedido->troco_para : null,
            'observacoes' => $pedido->observacoes,
            'distancia_km' => $pedido->distancia_km ? (float)$pedido->distancia_km : null,
            'tempo_espera_min' => $pedido->tempo_espera_min,
            'cancelado_por' => $pedido->cancelado_por,
            'cancelado_motivo' => $pedido->cancelado_motivo,
            'criado_em' => $formatarData($pedido->criado_em),
            'itens' => array_map(function($item) {
                return [
                    'id' => $item->id,
                    'produto_id' => $item->produto_id,
                    'nome' => $item->produto_nome,
                    'quantidade' => (int)$item->quantidade,
                    'preco_unitario' => (float)$item->preco_unitario,
                    'preco_total' => (float)$item->preco_total,
                    'observacao' => $item->observacao,
                    'opcoes' => $item->opcoes ? json_decode($item->opcoes) : null,
                ];
            }, $itens),
        ]);
    }

    /**
     * POST /api/app/pedido/cancelar
     */
    public function actionCancelar()
    {
        $request = Yii::$app->request;
        $usuarioId = Yii::$app->user->id;
        $pedidoId = $request->post('pedido_id');

        $pedido = Pedido::find()
            ->where(['id' => $pedidoId, 'usuario_id' => $usuarioId])
            ->one();

        if (!$pedido) {
            return ApiResponse::error('Pedido não encontrado', 404);
        }

        if (!in_array($pedido->status, ['pendente', 'confirmado'])) {
            return ApiResponse::error('Pedido não pode ser cancelado', 400);
        }

        $pedido->status = 'cancelado';
        $pedido->save();

        return ApiResponse::success(null, 'Pedido cancelado com sucesso');
    }

    // ==================== MÉTODOS AUXILIARES ====================
    /**
     * Envia notificação push para todos os lojistas da loja
     *
     * @param Pedido $pedido
     */
    private function enviarPushLojistas($pedido)
    {
        // 🔥 Recarrega o pedido com as relações necessárias
        $pedidoCompleto = Pedido::find()
            ->with(['usuario', 'itens'])
            ->where(['id' => $pedido->id])
            ->one();

        if (!$pedidoCompleto) {
            Yii::error('[PUSH] ❌ Pedido ' . $pedido->id . ' não encontrado para recarregar', __METHOD__);
            return;
        }

        Yii::info('[PUSH] 🔥 Iniciando envio para pedido ' . $pedidoCompleto->id . ' (loja ' . $pedidoCompleto->loja_id . ')', __METHOD__);

        try {
            // 1. Buscar lojistas vinculados à loja
            $lojistasVinculados = LojistaUsuarioLoja::find()
                ->where(['loja_id' => $pedidoCompleto->loja_id, 'status' => 1])
                ->all();

            Yii::info('[PUSH] 📋 Lojistas vinculados encontrados: ' . count($lojistasVinculados), __METHOD__);

            if (empty($lojistasVinculados)) {
                Yii::info('[PUSH] ⚠️ Nenhum lojista vinculado à loja ' . $pedidoCompleto->loja_id, __METHOD__);
                return;
            }

            $lojistaIds = array_column($lojistasVinculados, 'usuario_id');
            Yii::info('[PUSH] 📋 IDs dos lojistas vinculados: ' . implode(', ', $lojistaIds), __METHOD__);

            // 2. Filtrar apenas os que têm device_token
            $lojistas = LojistaUsuario::find()
                ->where(['id' => $lojistaIds])
                ->andWhere(['not', ['device_token' => null]])
                ->andWhere(['<>', 'device_token', ''])
                ->all();

            Yii::info('[PUSH] 📱 Lojistas com device_token: ' . count($lojistas), __METHOD__);

            if (empty($lojistas)) {
                Yii::info('[PUSH] ⚠️ Nenhum lojista com device_token', __METHOD__);
                return;
            }

            // 3. Instanciar FirebaseService
            try {
                $firebase = FirebaseService::getInstance();
                Yii::info('[PUSH] ✅ FirebaseService instanciado com sucesso', __METHOD__);
            } catch (\Exception $e) {
                Yii::error('[PUSH] ❌ Falha ao instanciar FirebaseService: ' . $e->getMessage(), __METHOD__);
                return;
            }

            $enviados = 0;

            // 4. Enviar para cada lojista
            foreach ($lojistas as $lojista) {
                try {
                    Yii::info('[PUSH] 📤 Enviando para lojista ' . $lojista->id . ' (token: ' . substr($lojista->device_token, 0, 10) . '...)', __METHOD__);

                    // 🔥 Envia usando o pedido completo (com relações)
                    $result = $firebase->sendPedidoNotification(
                        $lojista->device_token,
                        $pedidoCompleto,
                        'novo_pedido'
                    );

                    if ($result) {
                        $enviados++;
                        Yii::info('[PUSH] ✅ Enviado com sucesso para lojista ' . $lojista->id, __METHOD__);
                    } else {
                        Yii::warning('[PUSH] ⚠️ Envio retornou null para lojista ' . $lojista->id, __METHOD__);
                    }
                } catch (\Exception $e) {
                    Yii::error('[PUSH] ❌ Erro ao enviar para lojista ' . $lojista->id . ': ' . $e->getMessage(), __METHOD__);
                }
            }

            Yii::info('[PUSH] 🎯 Total enviados: ' . $enviados . ' de ' . count($lojistas), __METHOD__);

        } catch (\Exception $e) {
            Yii::error('[PUSH] ❌ Erro geral: ' . $e->getMessage(), __METHOD__);
        }
    }

    /**
     * Gera código amigável: AAAAMMDD-XXX (contador diário)
     */
    private function gerarCodigoPedido()
    {
        $prefixo = date('Ymd');

        $ultimoPedido = Pedido::find()
            ->where(['like', 'codigo', $prefixo . '-%', false])
            ->orderBy(['codigo' => SORT_DESC])
            ->one();

        if ($ultimoPedido) {
            $partes = explode('-', $ultimoPedido->codigo);
            $contador = intval(end($partes)) + 1;
        } else {
            $contador = 1;
        }

        return $prefixo . '-' . str_pad($contador, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calcula a distância entre dois pontos (Haversine)
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $earthRadius = 6371;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}