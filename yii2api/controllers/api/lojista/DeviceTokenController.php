<?php

namespace app\controllers\api\lojista;

use Yii;
use app\components\ApiResponse;
use app\controllers\api\lojista\LojistaControllerBase;
use app\models\api\lojista\LojistaUsuario;

class DeviceTokenController extends LojistaControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * POST /api/lojista/device-token
     * Salva o token FCM do dispositivo do lojista
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $deviceToken = $request->getBodyParam('device_token');

        if (empty($deviceToken)) {
            return ApiResponse::error('device_token é obrigatório', 400);
        }

        $lojista = $this->getLojista();
        if (!$lojista) {
            return ApiResponse::error('Lojista não autenticado', 401);
        }

        $lojista->device_token = $deviceToken;
        $lojista->save(false);

        return ApiResponse::success([
            'message' => 'Token salvo com sucesso',
            'device_token' => $deviceToken,
        ]);
    }

    /**
     * DELETE /api/lojista/device-token
     * Remove o token FCM (logout)
     */
    public function actionDelete()
    {
        $lojista = $this->getLojista();
        if (!$lojista) {
            return ApiResponse::error('Lojista não autenticado', 401);
        }

        $lojista->device_token = null;
        $lojista->save(false);

        return ApiResponse::success([
            'message' => 'Token removido com sucesso',
        ]);
    }
}