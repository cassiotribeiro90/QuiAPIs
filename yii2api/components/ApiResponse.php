<?php
// components/ApiResponse.php

namespace app\components;

use Yii;

class ApiResponse
{
    /**
     * Resposta de sucesso
     */
    public static function success($data = null, $message = 'Operação realizada com sucesso', $code = 200, $status = null)
    {
        Yii::$app->response->statusCode = $code;
        
        $response = [
            'success' => true,
            'code' => $code,
            'message' => $message,
        ];
        
        if ($status !== null) {
            $response['status'] = $status; // Pode ser: 'warning', 'info', 'email_not_verified', etc
        }
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return $response;
    }
    
    /**
     * Resposta de erro
     */
    public static function error($message = 'Erro na operação', $code = 400, $status = null, $errors = null)
    {
        Yii::$app->response->statusCode = $code;
        
        $response = [
            'success' => false,
            'code' => $code,
            'message' => $message,
        ];
        
        if ($status !== null) {
            $response['status'] = $status; // Ex: 'invalid_credentials', 'duplicate_email', etc
        }
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        return $response;
    }
}