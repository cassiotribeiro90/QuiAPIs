<?php

namespace app\controllers\api\lojista;

use Yii;
use yii\rest\Controller;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;

class LojistaControllerBase extends Controller
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
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => ['*'],
            ],
        ];

        // 🔥 AUTENTICAÇÃO VIA userLojista - CORRIGIDO
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'user' => Yii::$app->userLojista,  // ← Passa o OBJETO, não a string
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

    /**
     * Resposta para requisições OPTIONS (CORS preflight)
     */
    public function actionOptions()
    {
        Yii::$app->response->headers->set('Allow', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        Yii::$app->response->statusCode = 200;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => true];
    }

    /**
     * Retorna o lojista autenticado
     * 
     * @return \app\models\api\lojista\LojistaUsuario|null
     */
    protected function getLojista()
    {
        return Yii::$app->userLojista->identity;
    }

    /**
     * Retorna o ID do lojista autenticado
     * 
     * @return int|null
     */
    protected function getLojistaId()
    {
        $lojista = $this->getLojista();
        return $lojista ? $lojista->id : null;
    }

    /**
     * Verifica se o lojista está autenticado
     * 
     * @return bool
     */
    protected function isLojistaAutenticado()
    {
        return !Yii::$app->userLojista->isGuest;
    }
}