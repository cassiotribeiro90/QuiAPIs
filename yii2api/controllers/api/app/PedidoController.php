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
use app\models\api\app\Usuario;

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
            return 10.00; // Taxa fixa para até 5 km
        } elseif ($distancia <= 10) {
            return 15.00; // Taxa fixa para entre 5 e 10 km
        } else {
            return 20.00; // Taxa fixa para acima de 10 km
        }
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

            // 2. Endereço
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

            // Valida contra as configurações da loja (se existirem)
            $loja = Loja::findOne($carrinho->loja_id);
            if (!$loja) {
                return ApiResponse::error('Loja não encontrada', 404);
            }

            $formasValidas = ['dinheiro', 'cartao_entrega']; // fallback mínimo
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

            // 4. Calcular frete (usando método interno)
            $distancia = $this->calcularDistancia(
                $loja->latitude,
                $loja->longitude,
                $endereco->latitude,
                $endereco->longitude
            );
            $taxaEntrega = $this->calcularTaxaEntrega($distancia);

            // 5. Criar pedido
            $pedido = new Pedido();
            $pedido->usuario_id    = $usuarioId;
            $pedido->loja_id       = $carrinho->loja_id;
            $pedido->endereco_id   = $enderecoId;
            $pedido->subtotal      = $carrinho->subtotal;
            $pedido->taxa_entrega  = $taxaEntrega;
            $pedido->total         = $carrinho->subtotal + $taxaEntrega;
            $pedido->forma_pagamento = $formaPagamento;
            $pedido->codigo = 'PED-' . date('YmdHis') . '-' . str_pad(rand(0, 9999), 6, '0', STR_PAD_LEFT);
            $pedido->status        =  Pedido::STATUS_NOVO; // 'novo'
            $pedido->pagamento_status = Pedido::PAGAMENTO_PENDENTE;   // ← nome correto da coluna
            $pedido->observacoes    = $request->post('observacao');

            if ($formaPagamento === 'dinheiro') {
                $pedido->troco_para = $request->post('troco_para');
            }

            if (!$pedido->save()) {
                $errors = json_encode($pedido->errors);
                Yii::error("Erro ao salvar pedido: $errors", __METHOD__);
                throw new \Exception('Erro ao salvar pedido: ' . $errors);
            }

            // 6. Transferir itens do carrinho para pedido_item
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

            // 7. Esvaziar carrinho
            $carrinho->delete();

            $transaction->commit();

            return ApiResponse::success([
                'pedido_id'       => $pedido->id,
                'total'           => $pedido->total,
                'status'          => $pedido->status,
                'forma_pagamento' => $pedido->forma_pagamento,
                'criado_em'       => $pedido->criado_em,
            ], 'Pedido criado com sucesso', 201);

        } catch (\Exception $e) {
            $transaction->rollBack();
            $message = $e->getMessage();
            Yii::error("Erro ao criar pedido: $message", __METHOD__);
            return ApiResponse::error('Erro ao processar pedido: ' . $message, 500);
        }
    }

    /**
     * GET /api/app/pedido/historico
     * Lista pedidos do usuário com total de itens em cada pedido
     */
    public function actionHistorico()
    {
        // Obter usuário autenticado via token Bearer
        $token = Yii::$app->request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);
        
        $usuario = Usuario::findIdentityByAccessToken($token);
        if (!$usuario) {
            return ApiResponse::error('Usuário não autenticado', 401);
        }
        $usuarioId = $usuario->id;
        
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
            // Contar itens do pedido
            $itemCount = PedidoItem::find()
                ->where(['pedido_id' => $pedido->id])
                ->count();
            
            return [
                'id' => $pedido->id,
                'codigo' => $pedido->codigo,
                'loja_nome' => $pedido->loja->nome ?? null,
                'total' => (float)$pedido->total,
                'status' => $pedido->status,
                'forma_pagamento' => $pedido->forma_pagamento,
                'item_count' => $itemCount,
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
     * GET /api/app/pedido/view?id=7
     * Detalhes de um pedido específico
     */
    public function actionView($id = null)
    {
        // 1. Obter ID da query string se não veio na URL
        if ($id === null) {
            $id = Yii::$app->request->get('pedido_id');
        }
        
        // 2. Obter usuário autenticado
        // Obter o token do header
        $token = Yii::$app->request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);
        
        // Buscar usuário pelo token
        $usuario = Usuario::findIdentityByAccessToken($token);
        if (!$usuario) {
            return ApiResponse::error('Usuário não autenticado', 401);
        }
        $usuarioId = $usuario->id;  
        // 3. Log para debug
        Yii::error("PedidoView - Buscando ID: " . var_export($id, true) . ", Usuário: " . $usuarioId, __METHOD__);
        
        // 4. Buscar pedido SEM filtro de usuário primeiro (para debug)
        $pedido = Pedido::findOne($id);
        
        if (!$pedido) {
            Yii::error("PedidoView - Pedido ID $id NÃO ENCONTRADO na tabela", __METHOD__);
            return ApiResponse::error('Pedido não encontrado (ID inexistente)', 404);
        }
        
        // 5. Verificar se pertence ao usuário
        if ($pedido->usuario_id != $usuarioId) {
            Yii::error("PedidoView - Pedido ID $id pertence ao usuário {$pedido->usuario_id}, não ao {$usuarioId}", __METHOD__);
            return ApiResponse::error('Pedido não encontrado (não pertence a este usuário)', 404);
        }
        
        // 6. Buscar itens
        $itens = PedidoItem::find()
            ->where(['pedido_id' => $pedido->id])
            ->all();
        
        // 7. Buscar endereço
        $endereco = $pedido->endereco;
        
        // 8. Buscar loja
        $loja = $pedido->loja;
        
        // 9. Montar resposta
        return ApiResponse::success([
            'id' => $pedido->id,
            'codigo' => $pedido->codigo,
            'status' => $pedido->status,
            'status_historico' => $pedido->status_historico,
            'data_pedido' => $pedido->data_pedido,
            'data_confirmacao' => $pedido->data_confirmacao,
            'data_preparo' => $pedido->data_preparo,
            'data_saida' => $pedido->data_saida,
            'data_entrega' => $pedido->data_entrega,
            'data_cancelamento' => $pedido->data_cancelamento,
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
            'criado_em' => $pedido->criado_em,
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