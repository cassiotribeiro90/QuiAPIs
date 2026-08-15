<?php

namespace app\controllers\api\lojista;

use Yii;
use app\components\ApiResponse;
use app\models\api\lojista\LojistaUsuarioLoja;
use app\models\api\app\Pedido;
use app\controllers\api\lojista\LojistaControllerBase;

class LojistaPedidoController extends LojistaControllerBase
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [];
        }

        return $behaviors;
    }

    /**
     * GET /api/lojista/pedidos
     */
    public function actionIndex()
    {
        try {
            $lojista = $this->getLojista();
            if (!$lojista) {
                return ApiResponse::error('Lojista não autenticado', 401);
            }

            $lojaIds = LojistaUsuarioLoja::find()
                ->select('loja_id')
                ->where(['usuario_id' => $lojista->id, 'status' => LojistaUsuarioLoja::STATUS_ATIVO])
                ->column();

            if (empty($lojaIds)) {
                return ApiResponse::success(['items' => []], 'Nenhuma loja associada');
            }

            $query = Pedido::find()
                ->where(['in', 'loja_id', $lojaIds])
                ->andWhere(['deletado_em' => null]);

            $status = Yii::$app->request->get('status');
            if (!empty($status)) {
                $query->andWhere(['status' => $status]);
            }

            $lojaId = Yii::$app->request->get('loja_id');
            if (!empty($lojaId)) {
                $query->andWhere(['loja_id' => $lojaId]);
            }

            $pedidos = $query->orderBy(['criado_em' => SORT_DESC])->all();

            $data = array_map(function($pedido) {
                return $this->formatPedidoResumido($pedido);
            }, $pedidos);

            return ApiResponse::success(['items' => $data], 'Pedidos listados');
        } catch (\Exception $e) {
            Yii::error("Erro ao listar pedidos: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao listar pedidos', 500);
        }
    }

    /**
     * GET /api/lojista/pedidos/{id}
     */
    public function actionView($id)
    {
        try {
            $lojista = $this->getLojista();
            if (!$lojista) {
                return ApiResponse::error('Lojista não autenticado', 401);
            }

            $pedido = Pedido::find()
                ->where(['id' => $id, 'deletado_em' => null])
                ->one();

            if (!$pedido) {
                return ApiResponse::error('Pedido não encontrado', 404);
            }

            $temAcesso = LojistaUsuarioLoja::find()
                ->where([
                    'usuario_id' => $lojista->id,
                    'loja_id' => $pedido->loja_id,
                    'status' => LojistaUsuarioLoja::STATUS_ATIVO,
                ])
                ->exists();

            if (!$temAcesso) {
                return ApiResponse::error('Você não tem acesso a este pedido', 403);
            }

            return ApiResponse::success($this->formatPedidoDetalhado($pedido), 'Pedido encontrado');
        } catch (\Exception $e) {
            Yii::error("Erro ao ver pedido: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao buscar pedido', 500);
        }
    }

    /**
     * POST /api/lojista/pedidos/{id}/aceitar
     */
    public function actionAceitar($id)
    {
        return $this->alterarStatus($id, 'em_preparo', 'Pedido aceito');
    }

    /**
     * POST /api/lojista/pedidos/{id}/recusar
     */
    public function actionRecusar($id)
    {
        return $this->alterarStatus($id, 'recusado', 'Pedido recusado');
    }

    /**
     * POST /api/lojista/pedidos/{id}/status
     */
    public function actionAtualizarStatus($id)
    {
        $request = Yii::$app->request;
        $novoStatus = $request->getBodyParam('status');

        if (empty($novoStatus)) {
            return ApiResponse::error('Status é obrigatório', 400);
        }

        $statusPermitidos = ['pendente', 'em_preparo', 'saiu_para_entrega', 'entregue', 'cancelado', 'recusado'];
        if (!in_array($novoStatus, $statusPermitidos)) {
            return ApiResponse::error('Status inválido', 400);
        }

        return $this->alterarStatus($id, $novoStatus, 'Status atualizado');
    }

    // ==================== AUXILIARES ====================
    private function alterarStatus($id, $novoStatus, $mensagemSucesso)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $lojista = $this->getLojista();
            if (!$lojista) {
                return ApiResponse::error('Lojista não autenticado', 401);
            }

            $pedido = Pedido::find()
                ->where(['id' => $id, 'deletado_em' => null])
                ->one();

            if (!$pedido) {
                return ApiResponse::error('Pedido não encontrado', 404);
            }

            $temAcesso = LojistaUsuarioLoja::find()
                ->where([
                    'usuario_id' => $lojista->id,
                    'loja_id' => $pedido->loja_id,
                    'status' => LojistaUsuarioLoja::STATUS_ATIVO,
                ])
                ->exists();

            if (!$temAcesso) {
                return ApiResponse::error('Você não tem acesso a este pedido', 403);
            }

            $pedido->status = $novoStatus;
            $pedido->atualizado_em = date('Y-m-d H:i:s');
            if (!$pedido->save(false)) {
                $transaction->rollBack();
                return ApiResponse::error('Erro ao salvar status', 500);
            }

            $transaction->commit();

            return ApiResponse::success(
                $this->formatPedidoResumido($pedido),
                $mensagemSucesso
            );
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Erro ao alterar status: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao alterar status', 500);
        }
    }

    private function formatPedidoResumido($pedido)
    {
        return [
            'id' => (int)$pedido->id,
            'cliente_nome' => $pedido->cliente_nome ?? $pedido->usuario->nome ?? null,
            'loja_id' => (int)$pedido->loja_id,
            'status' => $pedido->status,
            'total' => (float)$pedido->total,
            'criado_em' => $pedido->criado_em,
        ];
    }

    private function formatPedidoDetalhado($pedido)
    {
        return [
            'id' => (int)$pedido->id,
            'cliente_nome' => $pedido->cliente_nome ?? $pedido->usuario->nome ?? null,
            'cliente_telefone' => $pedido->usuario->telefone ?? null,
            'loja_id' => (int)$pedido->loja_id,
            'status' => $pedido->status,
            'subtotal' => (float)$pedido->subtotal,
            'taxa_entrega' => (float)$pedido->taxa_entrega,
            'total' => (float)$pedido->total,
            'endereco_entrega' => $pedido->endereco_completo,
            'itens' => $pedido->itens,
            'observacoes' => $pedido->observacoes,
            'forma_pagamento' => $pedido->forma_pagamento,
            'troco_para' => $pedido->troco_para,
            'criado_em' => $pedido->criado_em,
        ];
    }
}