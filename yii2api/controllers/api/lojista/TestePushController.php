<?php

namespace app\controllers\api\lojista;

use Yii;
use app\components\FirebaseService;
use app\components\ApiResponse;
use app\controllers\api\lojista\LojistaControllerBase;

class TestePushController extends LojistaControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * POST /api/lojista/teste-push
     * Envia uma notificação de teste
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $deviceToken = $request->getBodyParam('device_token');
        $title = $request->getBodyParam('title', '🧪 Notificação de Teste');
        $body = $request->getBodyParam('body', 'Esta é uma notificação de teste do QuiManda!');

        if (empty($deviceToken)) {
            return ApiResponse::error('device_token é obrigatório', 400);
        }

        try {
            $firebase = FirebaseService::getInstance();
            
            $result = $firebase->sendNotification(
                $deviceToken,
                $title,
                $body,
                [
                    'type' => 'teste',
                    'timestamp' => date('Y-m-d H:i:s'),
                ]
            );

            if ($result) {
                return ApiResponse::success([
                    'message' => '✅ Notificação enviada com sucesso!',
                    'result' => json_encode($result),
                ]);
            } else {
                return ApiResponse::error('❌ Erro ao enviar notificação', 500);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('❌ Erro: ' . $e->getMessage(), 500);
        }
    }
}