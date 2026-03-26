<?php
namespace app\controllers\api\app;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;

class AppControllerBase extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // CORS
        $behaviors['cors'] = [
            'class' => Cors::class,
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
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        
        // 🔥 AUTENTICAÇÃO - APENAS PARA ROTAS QUE PRECISAM
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => [
                'index',           // listagem de lojas
                'view',            // visualizar loja
                'proximas',        // lojas próximas
                'options',         // preflight CORS
                'login',           // login
                'cadastro',        // cadastro
                'refresh-token',   // renovar token
                'categorias',      // listar categorias
                'produtos',        // listar produtos
                'produto-view',    // visualizar produto
            ],
        ];
        
        return $behaviors;
    }
    
    /**
     * Ação para OPTIONS (preflight CORS)
     */
    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
        return '';
    }

    /**
     * Extrai usuário do token no header (para rotas autenticadas)
     * @return \app\models\api\app\Usuario
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function getUserByToken()
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');
        
        if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            throw new \yii\web\UnauthorizedHttpException('Token não fornecido');
        }
        
        $token = $matches[1];
        $usuario = \app\models\api\app\Usuario::findIdentityByAccessToken($token);
        
        if (!$usuario) {
            throw new \yii\web\UnauthorizedHttpException('Token inválido ou expirado');
        }
        
        return $usuario;
    }
    
    /**
     * Formata resposta de sucesso
     */
    protected function successResponse($data = null, $message = 'Operação realizada com sucesso', $code = 200)
    {
        Yii::$app->response->statusCode = $code;
        return [
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
    
    /**
     * Formata resposta de erro
     */
    protected function errorResponse($message, $code = 400, $internalCode = null, $errors = null)
    {
        Yii::$app->response->statusCode = $code;
        $response = [
            'success' => false,
            'code' => $code,
            'message' => $message,
        ];
        
        if ($internalCode) {
            $response['internal_code'] = $internalCode;
        }
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return $response;
    }
}