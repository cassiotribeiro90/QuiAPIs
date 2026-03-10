<?php
// controllers/api/gestor/DashboardController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\controllers\api\gestor\ControllerBase;

class DashboardController extends ControllerBase
{
    /**
     * GET /api/gestor/dashboard
     * Retorna dados analíticos do dashboard
     */
    public function actionIndex()
    {
        try {
           $this->getUserByToken(); // Apenas autenticado
            
            // Dados mockados por enquanto
            $dados = [
                'lojas' => [
                    'total' => 142,
                    'ativas' => 138,
                    'inativas' => 4,
                ],
                'pedidos' => [
                    'hoje' => 104,
                    'semana' => 465,
                    'mes' => 4205,
                    'ano' => 32043,
                    'total' => 253250,
                    'ultima_atualizacao' => date('Y-m-d H:i:s'),
                ],
                'faturamento' => [
                    'hoje' => 'R$ 5.420',
                    'semana' => 'R$ 23.850',
                    'mes' => 'R$ 189.450',
                    'ano' => 'R$ 1.452.780',
                ],
                'metricas' => [
                    'ticket_medio' => 'R$ 52,30',
                    'lojas_ativas_percent' => 97,
                    'crescimento_mensal' => '+12%',
                ]
            ];
            
            return ApiResponse::success($dados, 'Dashboard carregado com sucesso');
            
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->statusCode ?? 401,
                'unauthorized'
            );
        }
    }
    
    /**
     * GET /api/gestor/dashboard/graficos
     * Dados para gráficos
     */
    public function actionGraficos()
    {
        try {
            $this->getUserByToken();
            
            return ApiResponse::success([
                'vendas_ultimos_30_dias' => [
                    ['dia' => '01/03', 'vendas' => 45],
                    ['dia' => '02/03', 'vendas' => 52],
                    ['dia' => '03/03', 'vendas' => 48],
                    ['dia' => '04/03', 'vendas' => 61],
                    ['dia' => '05/03', 'vendas' => 55],
                    ['dia' => '06/03', 'vendas' => 70],
                    ['dia' => '07/03', 'vendas' => 83],
                ],
                'top_categorias' => [
                    ['categoria' => 'Restaurantes', 'total' => 45],
                    ['categoria' => 'Pizzarias', 'total' => 32],
                    ['categoria' => 'Hamburguerias', 'total' => 28],
                    ['categoria' => 'Mercados', 'total' => 20],
                    ['categoria' => 'Padarias', 'total' => 17],
                ]
            ]);
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        }
    }
}