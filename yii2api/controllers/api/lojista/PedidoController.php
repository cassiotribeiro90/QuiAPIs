<?php
namespace app\controllers\api\lojista;

use yii\rest\Controller;

class PedidoController extends Controller
{
    // GET /pedido
    public function actionIndex()
    {
        return [
            'status' => 'ok',
            'api' => 'Lojista',
            'mensagem' => 'Lista de pedidos (mock)',
            'dados' => [
                ['id' => 1, 'cliente' => 'João', 'valor' => 150.00],
                ['id' => 2, 'cliente' => 'Maria', 'valor' => 230.50],
                ['id' => 3, 'cliente' => 'Pedro', 'valor' => 89.90],
            ]
        ];
    }
    
    // GET /pedido/123
    public function actionView($id)
    {
        return [
            'status' => 'ok',
            'api' => 'Lojista',
            'mensagem' => "Detalhes do pedido #$id",
            'dados' => [
                'id' => $id,
                'cliente' => 'Cliente exemplo',
                'valor' => 150.00,
                'itens' => ['Item 1', 'Item 2']
            ]
        ];
    }
    
    // POST /pedido
    public function actionCreate()
    {
        $dados = \Yii::$app->request->post();
        
        return [
            'status' => 'ok',
            'api' => 'Lojista',
            'mensagem' => 'Pedido criado com sucesso (mock)',
            'dados_recebidos' => $dados
        ];
    }
}