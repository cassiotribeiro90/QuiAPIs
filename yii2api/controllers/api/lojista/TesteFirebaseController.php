<?php

namespace app\controllers\api\lojista;

use Yii;
use app\components\FirebaseService;
use app\components\ApiResponse;
use app\controllers\api\lojista\LojistaControllerBase;

class TesteFirebaseController extends LojistaControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/lojista/teste-firebase
     * Testa a conexão com o Firebase
     */
    public function actionIndex()
    {
        try {
            $firebase = FirebaseService::getInstance();
            
            return ApiResponse::success([
                'message' => '✅ Firebase conectado com sucesso!',
                'status' => 'ok',
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('❌ Erro ao conectar Firebase: ' . $e->getMessage(), 500);
        }
    }
}