<?php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\controllers\api\gestor\ControllerBase;
use app\models\api\gestor\Loja;
use app\models\api\app\Pedido;
use app\models\api\app\Usuario;
use app\models\api\app\Avaliacao;
use yii\web\UnauthorizedHttpException;

class DashboardController extends ControllerBase
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        try {
            // 🔥 VALIDA O TOKEN PRIMEIRO (LANÇA 401 SE INVÁLIDO)
            $this->getUserByToken();

            $data = [
                'kpis' => $this->getKpis(),
                'pedidos_por_dia' => $this->getPedidosPorDia(),
                'faturamento_por_mes' => $this->getFaturamentoPorMes(),
                'pedidos_por_status' => $this->getPedidosPorStatus(),
                'pedidos_por_pagamento' => $this->getPedidosPorPagamento(),
                'top_lojas_faturamento' => $this->getTopLojasFaturamento(),
                'top_lojas_pedidos' => $this->getTopLojasPedidos(),
                'top_produtos' => $this->getTopProdutos(),
                'top_clientes' => $this->getTopClientes(),
                'lojas_por_categoria' => $this->getLojasPorCategoria(),
                'lojas_por_cidade' => $this->getLojasPorCidade(),
                'horarios_pico' => $this->getHorariosPico(),
                'satisfacao' => $this->getSatisfacao(),
            ];

            return ApiResponse::success($data, 'Dashboard carregado com sucesso');

        } catch (UnauthorizedHttpException $e) {
            // 🔥 RETORNA 401 PARA TOKEN INVÁLIDO/EXPIRADO
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (\Exception $e) {
            // 🔥 OUTROS ERROS → 500
            return ApiResponse::error(
                $e->getMessage(),
                500,
                'internal_error'
            );
        }
    }

    private function getKpis()
    {
        $hoje = date('Y-m-d');
        $semana = date('Y-m-d', strtotime('-7 days'));
        $mes = date('Y-m-d', strtotime('-30 days'));

        return [
            'lojas_ativas' => Loja::find()->where(['status' => 'ativo'])->count(),
            'lojas_total' => Loja::find()->count(),
            'lojas_verificadas' => Loja::find()->where(['verificado' => 1])->count(),
            'pedidos_hoje' => Pedido::find()->where(['>=', 'data_pedido', $hoje])->count(),
            'pedidos_semana' => Pedido::find()->where(['>=', 'data_pedido', $semana])->count(),
            'pedidos_mes' => Pedido::find()->where(['>=', 'data_pedido', $mes])->count(),
            'pedidos_total' => Pedido::find()->count(),
            'faturamento_hoje' => Pedido::find()
                ->where(['>=', 'data_pedido', $hoje])
                ->andWhere(['!=', 'status', 'cancelado'])
                ->sum('total') ?? 0,
            'faturamento_semana' => Pedido::find()
                ->where(['>=', 'data_pedido', $semana])
                ->andWhere(['!=', 'status', 'cancelado'])
                ->sum('total') ?? 0,
            'faturamento_mes' => Pedido::find()
                ->where(['>=', 'data_pedido', $mes])
                ->andWhere(['!=', 'status', 'cancelado'])
                ->sum('total') ?? 0,
            'ticket_medio' => Pedido::find()
                ->where(['!=', 'status', 'cancelado'])
                ->average('total') ?? 0,
            'clientes_ativos' => Usuario::find()
                ->where(['tipo' => 'cliente'])
                ->andWhere(['status' => 'ativo'])
                ->count(),
            'clientes_total' => Usuario::find()
                ->where(['tipo' => 'cliente'])
                ->count(),
            'avaliacao_media' => Avaliacao::find()
                ->where(['status' => 'aprovado'])
                ->average('nota') ?? 0,
            'taxa_cancelamento' => $this->getTaxaCancelamento(),
            'distancia_media' => Pedido::find()
                ->where(['!=', 'status', 'cancelado'])
                ->average('distancia_km') ?? 0,
        ];
    }

    private function getTaxaCancelamento()
    {
        $total = Pedido::find()->count();
        if ($total == 0) return 0;
        $cancelados = Pedido::find()->where(['status' => 'cancelado'])->count();
        return round(($cancelados / $total) * 100, 2);
    }

    private function getPedidosPorDia()
    {
        $sql = "SELECT DATE(data_pedido) as dia, COUNT(*) as total 
                FROM pedido 
                WHERE data_pedido >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(data_pedido)
                ORDER BY dia ASC";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    private function getFaturamentoPorMes()
    {
        $sql = "SELECT DATE_FORMAT(data_pedido, '%Y-%m') as mes, SUM(total) as total 
                FROM pedido 
                WHERE data_pedido >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                AND status != 'cancelado'
                GROUP BY DATE_FORMAT(data_pedido, '%Y-%m')
                ORDER BY mes ASC";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    private function getPedidosPorStatus()
    {
        return Pedido::find()
            ->select(['status', 'COUNT(*) as total'])
            ->groupBy('status')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();
    }

    private function getPedidosPorPagamento()
    {
        return Pedido::find()
            ->select(['forma_pagamento', 'COUNT(*) as total'])
            ->groupBy('forma_pagamento')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();
    }

    private function getTopLojasFaturamento()
    {
        $sql = "SELECT l.nome, SUM(p.total) as faturamento 
                FROM pedido p
                JOIN loja l ON l.id = p.loja_id
                WHERE p.status != 'cancelado'
                GROUP BY p.loja_id
                ORDER BY faturamento DESC
                LIMIT 5";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    private function getTopLojasPedidos()
    {
        $sql = "SELECT l.nome, COUNT(*) as total_pedidos 
                FROM pedido p
                JOIN loja l ON l.id = p.loja_id
                GROUP BY p.loja_id
                ORDER BY total_pedidos DESC
                LIMIT 5";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    private function getTopProdutos()
    {
        $sql = "SELECT nome, vendas_hoje as vendas 
                FROM produto 
                WHERE vendas_hoje > 0
                ORDER BY vendas_hoje DESC
                LIMIT 5";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    private function getTopClientes()
    {
        $sql = "SELECT nome, total_gasto, total_pedidos 
                FROM app_usuario 
                WHERE tipo = 'cliente'
                AND nome IS NOT NULL
                AND nome != ''
                ORDER BY total_gasto DESC
                LIMIT 5";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    private function getLojasPorCategoria()
    {
        return Loja::find()
            ->select(['categoria', 'COUNT(*) as total'])
            ->groupBy('categoria')
            ->orderBy(['total' => SORT_DESC])
            ->limit(8)
            ->asArray()
            ->all();
    }

    private function getLojasPorCidade()
    {
        return Loja::find()
            ->select(['cidade', 'uf', 'COUNT(*) as total'])
            ->groupBy(['cidade', 'uf'])
            ->orderBy(['total' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
    }

    private function getHorariosPico()
    {
        $sql = "SELECT HOUR(data_pedido) as hora, COUNT(*) as total 
                FROM pedido 
                GROUP BY HOUR(data_pedido)
                ORDER BY total DESC
                LIMIT 8";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    private function getSatisfacao()
    {
        $total = Avaliacao::find()->where(['status' => 'aprovado'])->count();
        $positivas = Avaliacao::find()
            ->where(['status' => 'aprovado'])
            ->andWhere(['>=', 'nota', 4])
            ->count();
        $negativas = Avaliacao::find()
            ->where(['status' => 'aprovado'])
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