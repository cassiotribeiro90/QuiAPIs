<?php
// controllers/api/app/PedidoController.php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\models\api\app\Pedido;
use app\models\api\app\PedidoItem;
use app\models\api\app\Carrinho;
use app\models\api\app\CarrinhoItem;
use app\models\api\app\Loja;
use app\models\api\app\AppEndereco;
use app\controllers\api\app\AppControllerBase;
use app\models\common\CalcularTaxaDeEntrega;

class PedidoController extends AppControllerBase
{
    /**
     * {@inheritdoc}
     * Todos os endpoints exigem autenticação
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // Nenhuma ação pública – todas exigem token
        return $behaviors;
    }

    /**
     * POST /api/app/pedido/calcular-frete
     * Calcula a taxa de entrega com base na distância entre loja e endereço
     * 
     * Body: { loja_id, endereco_id }
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

        $taxa = CalcularTaxaDeEntrega::calcular($distancia);

        return ApiResponse::success([
            'distancia_km' => round($distancia, 2),
            'taxa_entrega' => $taxa,
            'taxa_entrega_formatada' => 'R$ ' . number_format($taxa, 2, ',', '.'),
        ]);
    }

    /**
     * POST /api/app/pedido/criar
     * Cria um novo pedido a partir do carrinho ativo
     * 
     * Body: { endereco_id, forma_pagamento, troco_para?, observacao? }
     */
    public function actionCriar()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $request = Yii::$app->request;
            $usuarioId = Yii::$app->user->id;

            // 1. Buscar carrinho ativo
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

            // 2. Validar endereço
            $enderecoId = $request->post('endereco_id');
            $endereco = AppEndereco::find()
                ->where(['id' => $enderecoId, 'usuario_id' => $usuarioId])
                ->one();

            if (!$endereco) {
                return ApiResponse::error('Endereço inválido', 400);
            }

            // 3. Forma de pagamento
            $formaPagamento = $request->post('forma_pagamento');
            $formasValidas = ['dinheiro', 'cartao_entrega', 'pix', 'cartao_online'];
            if (!in_array($formaPagamento, $formasValidas)) {
                return ApiResponse::error('Forma de pagamento inválida', 400);
            }

            // 4. Calcular frete
            $loja = Loja::findOne($carrinho->loja_id);
            $distancia = $this->calcularDistancia(
                $loja->latitude, $loja->longitude,
                $endereco->latitude, $endereco->longitude
            );
            $taxaEntrega = $this->calcularTaxaEntrega($distancia);

            // 5. Criar pedido
            $pedido = new Pedido();
            $pedido->usuario_id = $usuarioId;
            $pedido->loja_id = $carrinho->loja_id;
            $pedido->endereco_id = $enderecoId;
            $pedido->subtotal = $carrinho->subtotal;
            $pedido->taxa_entrega = $taxaEntrega;
            $pedido->total = $carrinho->subtotal + $taxaEntrega;
            $pedido->forma_pagamento = $formaPagamento;
            $pedido->status = 'pendente';
            $pedido->status_pagamento = 'pendente';
            $pedido->observacao = $request->post('observacao');

            if ($formaPagamento === 'dinheiro') {
                $pedido->troco_para = $request->post('troco_para');
            }

            if (!$pedido->save()) {
                throw new \Exception('Erro ao criar pedido: ' . json_encode($pedido->errors));
            }

            // 6. Transferir itens do carrinho para pedido_item
            foreach ($itensCarrinho as $item) {
                $pedidoItem = new PedidoItem();
                $pedidoItem->pedido_id = $pedido->id;
                $pedidoItem->produto_id = $item->produto_id;
                $pedidoItem->nome = $item->produto_nome;
                $pedidoItem->quantidade = $item->quantidade;
                $pedidoItem->preco_unitario = $item->preco_unitario;
                $pedidoItem->preco_total = $item->preco_total;
                $pedidoItem->observacao = $item->observacao;
                $pedidoItem->opcoes = $item->opcoes;
                $pedidoItem->save();
            }

            // 7. Esvaziar carrinho
            $carrinho->delete();

            $transaction->commit();

            return ApiResponse::success([
                'pedido_id' => $pedido->id,
                'total' => $pedido->total,
                'status' => $pedido->status,
                'forma_pagamento' => $pedido->forma_pagamento,
                'criado_em' => $pedido->criado_em,
            ], 'Pedido criado com sucesso', 201);

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Erro ao criar pedido: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao processar pedido', 500);
        }
    }

    /**
     * GET /api/app/pedido/historico
     * Lista pedidos do usuário
     */
    public function actionHistorico()
    {
        $usuarioId = Yii::$app->user->id;
        $page = (int)Yii::$app->request->get('page', 1);
        $perPage = (int)Yii::$app->request->get('per_page', 10);

        $query = Pedido::find()
            ->where(['usuario_id' => $usuarioId])
            ->orderBy(['criado_em' => SORT_DESC]);

        $total = $query->count();
        $pedidos = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->all();

        $data = array_map(function($pedido) {
            return [
                'id' => $pedido->id,
                'loja_nome' => $pedido->loja->nome ?? null,
                'total' => (float)$pedido->total,
                'status' => $pedido->status,
                'forma_pagamento' => $pedido->forma_pagamento,
                'criado_em' => $pedido->criado_em,
            ];
        }, $pedidos);

        return ApiResponse::success([
            'items' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage),
            ],
        ]);
    }

    /**
     * GET /api/app/pedido/{id}
     * Detalhes de um pedido específico
     */
    public function actionView($id)
    {
        $usuarioId = Yii::$app->user->id;

        $pedido = Pedido::find()
            ->where(['id' => $id, 'usuario_id' => $usuarioId])
            ->one();

        if (!$pedido) {
            return ApiResponse::error('Pedido não encontrado', 404);
        }

        $itens = PedidoItem::find()
            ->where(['pedido_id' => $pedido->id])
            ->all();

        $endereco = $pedido->endereco;

        return ApiResponse::success([
            'id' => $pedido->id,
            'loja' => [
                'id' => $pedido->loja->id,
                'nome' => $pedido->loja->nome,
            ],
            'endereco' => [
                'logradouro' => $endereco->logradouro,
                'numero' => $endereco->numero,
                'complemento' => $endereco->complemento,
                'bairro' => $endereco->bairro,
                'cidade' => $endereco->cidade,
                'uf' => $endereco->uf,
            ],
            'subtotal' => (float)$pedido->subtotal,
            'taxa_entrega' => (float)$pedido->taxa_entrega,
            'total' => (float)$pedido->total,
            'status' => $pedido->status,
            'forma_pagamento' => $pedido->forma_pagamento,
            'status_pagamento' => $pedido->status_pagamento,
            'troco_para' => $pedido->troco_para,
            'observacao' => $pedido->observacao,
            'criado_em' => $pedido->criado_em,
            'itens' => array_map(function($item) {
                return [
                    'id' => $item->id,
                    'produto_id' => $item->produto_id,
                    'nome' => $item->nome,
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
     * Cancela um pedido (apenas se ainda estiver pendente)
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
     * Calcula a distância entre dois pontos (Haversine)
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}