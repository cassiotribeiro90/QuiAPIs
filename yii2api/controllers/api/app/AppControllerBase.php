<?php

namespace app\controllers\api\app;

use Yii;
use yii\rest\Controller;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;

class AppControllerBase extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // 🔥 CORS
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age' => 86400,
            ],
        ];

        // 🔥 AUTENTICAÇÃO ATIVA (padrão para todos os controllers)
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            // 🔥 EXCEÇÕES GLOBAIS (endpoints públicos)
            'except' => [
                'options',
            ],
        ];

        // 🔥 FORMATO JSON
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }
}