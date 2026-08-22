<?php

namespace app\components;

use Yii;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;

class FirebaseService
{
    private static $_instance = null;
    private $_messaging;
    
    private function __construct()
    {
        try {
            $credentials = Yii::$app->params['firebase']['credentials'] ?? __DIR__ . '/../config/firebase-credentials.json';
            $projectId = Yii::$app->params['firebase']['projectId'] ?? 'quimanda-5e5c9';
            
            $factory = (new Factory())
                ->withServiceAccount($credentials)
                ->withProjectId($projectId);
            
            $this->_messaging = $factory->createMessaging();
            
            Yii::info('[FIREBASE] Serviço inicializado com sucesso', __METHOD__);
        } catch (Exception $e) {
            Yii::error('[FIREBASE] Erro ao inicializar: ' . $e->getMessage(), __METHOD__);
            throw $e;
        }
    }
    
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Envia notificação push para um dispositivo (genérico)
     */
    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($data);
            
            $result = $this->_messaging->send($message);
            
            Yii::info('[FIREBASE] Notificação enviada para ' . substr($deviceToken, 0, 10) . '...', __METHOD__);
            return $result;
            
        } catch (Exception $e) {
            Yii::error('[FIREBASE] Erro ao enviar: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }
    
    /**
     * Envia notificação para múltiplos dispositivos (genérico)
     */
    public function sendMulticastNotification($deviceTokens, $title, $body, $data = [])
    {
        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);
            
            $result = $this->_messaging->sendMulticast($message, $deviceTokens);
            
            Yii::info('[FIREBASE] Notificações enviadas para ' . count($deviceTokens) . ' dispositivos', __METHOD__);
            return $result;
            
        } catch (Exception $e) {
            Yii::error('[FIREBASE] Erro ao enviar multicast: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }
    
    /**
     * Envia notificação de pedido com payload intermediário
     * 
     * @param string $deviceToken Token FCM do dispositivo
     * @param object $pedido Objeto do pedido (deve ter os campos: id, loja_id, cliente_nome, status, total, itens)
     * @param string $type Tipo da notificação (novo_pedido, status_update, etc)
     * @return mixed Resultado do envio ou null em caso de erro
     */
    public function sendPedidoNotification($deviceToken, $pedido, $type = 'novo_pedido')
    {
        try {
            $payload = $this->buildIntermediaryPayload($pedido, $type);

            // 🔥 LOG DO PAYLOAD (vai mostrar qual campo é array)
            Yii::info('[FIREBASE] Payload para depuração: ' . print_r($payload, true), __METHOD__);

            $notification = Notification::create(
                $payload['notification']['title'],
                $payload['notification']['body']
            );

            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($payload['data']);

            $result = $this->_messaging->send($message);

            Yii::info('[FIREBASE] Notificação enviada para pedido ' . $pedido->id, __METHOD__);
            return $result;
        } catch (Exception $e) {
            Yii::error('[FIREBASE] Erro: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine(), __METHOD__);
            return null;
        }
    }
        
    /**
     * Constrói o payload intermediário para notificações de pedido
     * 
     * @param object $pedido
     * @param string $type
     * @return array
     */
    private function buildIntermediaryPayload($pedido, $type)
    {
        // Títulos dinâmicos por status
        $statusLabels = [
            'novo' => '🍕 Novo Pedido!',
            'aguardando' => '⏳ Pedido Aguardando',
            'confirmado' => '✅ Pedido Confirmado',
            'preparando' => '👨‍🍳 Pedido em Preparo',
            'pronto' => '✅ Pedido Pronto!',
            'saiu' => '🛵 Pedido Saiu para Entrega',
            'entregue' => '📦 Pedido Entregue',
            'cancelado' => '❌ Pedido Cancelado',
        ];

        $status = (string) ($pedido->status ?? 'novo');
        $title = $statusLabels[$status] ?? '📋 Atualização do Pedido';

        // Busca nome do cliente
        $clienteNome = 'Cliente';
        if (isset($pedido->usuario) && is_object($pedido->usuario)) {
            $clienteNome = (string) $pedido->usuario->nome ?: 'Cliente';
        } elseif (!empty($pedido->cliente_nome)) {
            $clienteNome = (string) $pedido->cliente_nome;
        }

        // Contagem de itens
        $itemCount = 0;
        if (method_exists($pedido, 'getItens')) {
            $itemCount = (int) $pedido->getItens()->count();
        } elseif (is_array($pedido->itens)) {
            $itemCount = count($pedido->itens);
        }

        $total = is_numeric($pedido->total ?? 0) 
            ? number_format((float) $pedido->total, 2, ',', '.') 
            : '0,00';

        $body = 'Cliente: ' . $clienteNome . ' - ' . $itemCount . ' itens';

        // Construir ações (array de arrays)
        $actions = [];
        if ($type === 'novo_pedido') {
            $actions = [
                [
                    'id' => 'view',
                    'title' => 'Ver Pedido',
                    'type' => 'navigate',
                    'destino' => "/pedido/{$pedido->id}",
                ],
                [
                    'id' => 'aceitar',
                    'title' => '✅ Aceitar',
                    'type' => 'api',
                    'api_endpoint' => "/api/lojista/pedidos/{$pedido->id}/aceitar",
                ],
                [
                    'id' => 'recusar',
                    'title' => '❌ Recusar',
                    'type' => 'api',
                    'api_endpoint' => "/api/lojista/pedidos/{$pedido->id}/recusar",
                ],
            ];
        } else {
            $actions = [
                [
                    'id' => 'view',
                    'title' => 'Ver Pedido',
                    'type' => 'navigate',
                    'destino' => "/pedido/{$pedido->id}",
                ],
            ];
        }

        // 🔥 SERIALIZAR ARRAYS PARA JSON STRING
        $actionsJson = json_encode($actions, JSON_UNESCAPED_UNICODE);
        $extrasJson = '{}';

        return [
            'notification' => [
                'title' => (string) $title,
                'body' => (string) $body,
                'sound' => 'default',
                'badge' => 1,
            ],
            'data' => [
                'version' => '1.5',
                'timestamp' => date('c'),
                'type' => (string) $type,
                'pedido_id' => (string) $pedido->id,
                'loja_id' => (string) $pedido->loja_id,
                'cliente_nome' => (string) $clienteNome,
                'status' => (string) $status,
                'total' => (string) $total,
                'screen' => '/pedidos',
                'actions' => $actionsJson,  // 🔥 JSON string
                'extras' => $extrasJson,    // 🔥 JSON string
            ],
        ];
    }
}