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

    /**
     * Extrai usuário do token no header
     */
    protected function getUserByToken()
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');
        
        if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            throw new \yii\web\UnauthorizedHttpException('Token não fornecido');
        }
        
        $token = $matches[1];
        $gestor = \app\models\api\gestor\GestorUsuario::findIdentityByAccessToken($token);
        
        if (!$gestor) {
            throw new \yii\web\UnauthorizedHttpException('Token inválido ou expirado');
        }
        
        return $gestor;
    }
}