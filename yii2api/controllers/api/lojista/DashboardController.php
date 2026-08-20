<?php

namespace app\controllers\api\lojista;

use Yii;
use app\components\ApiResponse;
use app\controllers\api\lojista\LojistaControllerBase;
use app\models\api\app\Pedido;
use app\models\api\app\Usuario;
use app\models\api\app\Avaliacao;
use app\models\api\gestor\Produto;
use app\models\api\lojista\LojistaUsuarioLoja;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class DashboardController extends LojistaControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * Obtém o ID da loja a partir do header X-Store-Id
     * e valida se o lojista tem acesso a ela.
     */
    private function getLojaIdFromRequest()
    {
        $lojistaId = $this->getLojistaId();
        if (!$lojistaId) {
            throw new BadRequestHttpException('Lojista não autenticado');
        }

        $request = Yii::$app->request;
        $lojaId = $request->getHeaders()->get('X-Store-Id');

        // Fallback: tenta no body/query
        if (empty($lojaId)) {
            $lojaId = $request->getBodyParam('store_id') ?: $request->get('store_id');
        }

        if (empty($lojaId)) {
            throw new BadRequestHttpException('O header X-Store-Id é obrigatório');
        }

        $lojaId = (int)$lojaId;

        // Verifica se o lojista tem acesso a essa loja
        $acesso = LojistaUsuarioLoja::find()
            ->where([
                'usuario_id' => $lojistaId,
                'loja_id'    => $lojaId,
                'status'     => 1,
            ])
            ->exists();

        if (!$acesso) {
            throw new NotFoundHttpException('Loja não encontrada ou acesso negado');
        }

        return $lojaId;
    }

    /**
     * Action principal do dashboard.
     * GET /api/lojista/dashboard
     */
    public function actionIndex()
    {
        try {
            // Obtém o loja_id do header
            $lojaId = $this->getLojaIdFromRequest();

            $data = [
                'kpis' => $this->getKpis($lojaId),
                'pedidos_por_dia' => $this->getPedidosPorDia($lojaId),
                'faturamento_por_mes' => $this->getFaturamentoPorMes($lojaId),
                'pedidos_por_status' => $this->getPedidosPorStatus($lojaId),
                'pedidos_por_pagamento' => $this->getPedidosPorPagamento($lojaId),
                'top_produtos' => $this->getTopProdutos($lojaId),
                'top_clientes' => $this->getTopClientes($lojaId),
                'horarios_pico' => $this->getHorariosPico($lojaId),
                'satisfacao' => $this->getSatisfacao($lojaId),
            ];

            return ApiResponse::success($data, 'Dashboard carregado com sucesso');

        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500, 'internal_error');
        }
    }

    // ============================================================
    // MÉTODOS PRIVADOS DE ESTATÍSTICAS
    // ============================================================

    private function getKpis($lojaId)
    {
        $hoje = date('Y-m-d');
        $semana = date('Y-m-d', strtotime('-7 days'));
        $mes = date('Y-m-d', strtotime('-30 days'));

        return [
            'pedidos_hoje' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['>=', 'data_pedido', $hoje])
                ->count(),
            'pedidos_semana' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['>=', 'data_pedido', $semana])
                ->count(),
            'pedidos_mes' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['>=', 'data_pedido', $mes])
                ->count(),
            'pedidos_total' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->count(),
            'faturamento_hoje' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['>=', 'data_pedido', $hoje])
                ->andWhere(['!=', 'status', 'cancelado'])
                ->sum('total') ?? 0,
            'faturamento_semana' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['>=', 'data_pedido', $semana])
                ->andWhere(['!=', 'status', 'cancelado'])
                ->sum('total') ?? 0,
            'faturamento_mes' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['>=', 'data_pedido', $mes])
                ->andWhere(['!=', 'status', 'cancelado'])
                ->sum('total') ?? 0,
            'ticket_medio' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['!=', 'status', 'cancelado'])
                ->average('total') ?? 0,
            'clientes_unicos' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->distinct('usuario_id')
                ->count(),
            'avaliacao_media' => Avaliacao::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['status' => 'aprovado'])
                ->average('nota') ?? 0,
            'taxa_cancelamento' => $this->getTaxaCancelamento($lojaId),
            'distancia_media' => Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['!=', 'status', 'cancelado'])
                ->average('distancia_km') ?? 0,
        ];
    }

    private function getTaxaCancelamento($lojaId)
    {
        $total = Pedido::find()->where(['loja_id' => $lojaId])->count();
        if ($total == 0) return 0;
        $cancelados = Pedido::find()
            ->where(['loja_id' => $lojaId])
            ->andWhere(['status' => 'cancelado'])
            ->count();
        return round(($cancelados / $total) * 100, 2);
    }

    private function getPedidosPorDia($lojaId)
    {
        $sql = "SELECT DATE(data_pedido) as dia, COUNT(*) as total 
                FROM pedido 
                WHERE loja_id = :loja_id
                AND data_pedido >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(data_pedido)
                ORDER BY dia ASC";
        return Yii::$app->db->createCommand($sql, [':loja_id' => $lojaId])->queryAll();
    }

    private function getFaturamentoPorMes($lojaId)
    {
        $sql = "SELECT DATE_FORMAT(data_pedido, '%Y-%m') as mes, SUM(total) as total 
                FROM pedido 
                WHERE loja_id = :loja_id
                AND data_pedido >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                AND status != 'cancelado'
                GROUP BY DATE_FORMAT(data_pedido, '%Y-%m')
                ORDER BY mes ASC";
        return Yii::$app->db->createCommand($sql, [':loja_id' => $lojaId])->queryAll();
    }

    private function getPedidosPorStatus($lojaId)
    {
        return Pedido::find()
            ->select(['status', 'COUNT(*) as total'])
            ->where(['loja_id' => $lojaId])
            ->groupBy('status')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();
    }

    private function getPedidosPorPagamento($lojaId)
    {
        return Pedido::find()
            ->select(['forma_pagamento', 'COUNT(*) as total'])
            ->where(['loja_id' => $lojaId])
            ->groupBy('forma_pagamento')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();
    }

    private function getTopProdutos($lojaId)
    {
        return Produto::find()
            ->select(['nome', 'vendas_hoje as vendas'])
            ->where(['loja_id' => $lojaId])
            ->andWhere(['>', 'vendas_hoje', 0])
            ->orderBy(['vendas_hoje' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
    }

    private function getTopClientes($lojaId)
    {
        $sql = "SELECT u.nome, COUNT(p.id) as total_pedidos, SUM(p.total) as total_gasto
                FROM pedido p
                JOIN app_usuario u ON u.id = p.usuario_id
                WHERE p.loja_id = :loja_id
                AND u.nome IS NOT NULL
                AND u.nome != ''
                GROUP BY u.id, u.nome
                ORDER BY total_gasto DESC
                LIMIT 5";
        return Yii::$app->db->createCommand($sql, [':loja_id' => $lojaId])->queryAll();
    }

    private function getHorariosPico($lojaId)
    {
        $sql = "SELECT HOUR(data_pedido) as hora, COUNT(*) as total 
                FROM pedido 
                WHERE loja_id = :loja_id
                GROUP BY HOUR(data_pedido)
                ORDER BY total DESC
                LIMIT 8";
        return Yii::$app->db->createCommand($sql, [':loja_id' => $lojaId])->queryAll();
    }

    private function getSatisfacao($lojaId)
    {
        $total = Avaliacao::find()
            ->where(['loja_id' => $lojaId])
            ->andWhere(['status' => 'aprovado'])
            ->count();
        $positivas = Avaliacao::find()
            ->where(['loja_id' => $lojaId])
            ->andWhere(['status' => 'aprovado'])
            ->andWhere(['>=', 'nota', 4])
            ->count();
        $negativas = Avaliacao::find()
            ->where(['loja_id' => $lojaId])
            ->andWhere(['status' => 'aprovado'])
            ->andWhere(['<=', 'nota', 2])
            ->count();

        return [
            'total' => $total,
            'positivas' => $positivas,
            'negativas' => $negativas,
            'percentual_positivo' => $total > 0 ? round(($positivas / $total) * 100, 2) : 0,
            'percentual_negativo' => $total > 0 ? round(($negativas / $total) * 100, 2) : 0,
        ];
    }
}