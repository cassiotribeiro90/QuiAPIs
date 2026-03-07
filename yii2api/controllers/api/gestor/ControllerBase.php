<?php
namespace app\controllers\api\gestor;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class ControllerBase extends Controller
{
    public $enableCsrfValidation = false;
    
    public function beforeAction($action)
    {
        // FORÇA JSON em todas as respostas
        Yii::$app->response->format = Response::FORMAT_JSON;

        $headers = Yii::$app->response->headers;
        $headers->add('Access-Control-Allow-Origin', '*');
        $headers->add('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $headers->add('Access-Control-Allow-Credentials', 'true');
        $headers->add('Access-Control-Max-Age', '86400');
        
        // RESPOSTA RÁPIDA PARA OPTIONS (preflight)
        if (Yii::$app->request->method === 'OPTIONS') {
            Yii::$app->response->statusCode = 200;
            return false;
        }
        
        return parent::beforeAction($action);
    }
}