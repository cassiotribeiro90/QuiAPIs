<?php
// controllers/api/gestor/ControllerBase.php

namespace app\controllers\api\gestor;

use Yii;
use yii\rest\Controller;
use yii\web\Response;

class ControllerBase extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // CORS
        $behaviors['cors'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
            ],
        ];
        
        // Forçar JSON
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        
        return $behaviors;
    }
    
    /**
     * Ação para OPTIONS (preflight)
     */
    public function actionOptions()
    {
        return '';
    }
}