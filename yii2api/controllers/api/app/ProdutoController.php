<?php
namespace app\controllers\api\app;

use yii\rest\Controller;

class ProdutoController extends Controller
{
    // GET /produto
    public function actionIndex()
    {
        return [
            'status' => 'ok',
            'api' => 'App',
            'mensagem' => 'Lista de produtos (mock)',
            'dados' => [
                ['id' => 1, 'nome' => 'Smartphone', 'preco' => 1999.99],
                ['id' => 2, 'nome' => 'Notebook', 'preco' => 4500.00],
                ['id' => 3, 'nome' => 'Tablet', 'preco' => 1200.00],
            ]
        ];
    }
    
    // GET /produto/123
    public function actionView($id)
    {
        return [
            'status' => 'ok',
            'api' => 'App',
            'mensagem' => "Detalhes do produto #$id",
            'dados' => [
                'id' => $id,
                'nome' => 'Produto exemplo',
                'preco' => 99.90,
                'descricao' => 'Descrição do produto'
            ]
        ];
    }
    
    // POST /produto
    public function actionCreate()
    {
        $dados = \Yii::$app->request->post();
        
        return [
            'status' => 'ok',
            'api' => 'App',
            'mensagem' => 'Produto criado com sucesso (mock)',
            'dados_recebidos' => $dados
        ];
    }
}