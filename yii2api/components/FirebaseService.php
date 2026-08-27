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
     * Envia notificação de pedido para LOJISTA (app QuiManda)
     * 
     * @param string $deviceToken Token FCM do dispositivo
     * @param object $pedido Objeto do pedido
     * @param string $type Tipo da notificação (novo_pedido, status_update, etc)
     * @return mixed Resultado do envio ou null em caso de erro
     */
    public function sendPedidoNotification($deviceToken, $pedido, $type = 'novo_pedido')
    {
        try {
            $payload = $this->buildIntermediaryPayload($pedido, $type, 'lojista');

            $notification = Notification::create(
                $payload['notification']['title'],
                $payload['notification']['body']
            );

            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($payload['data']);

            $result = $this->_messaging->send($message);

            Yii::info('[FIREBASE] Notificação para lojista enviada para pedido ' . $pedido->id, __METHOD__);
            return $result;
        } catch (Exception $e) {
            Yii::error('[FIREBASE] Erro: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine(), __METHOD__);
            return null;
        }
    }

    /**
     * NOVO: Envia notificação de pedido para CLIENTE (app QuiPede)
     * 
     * @param string $deviceToken Token FCM do dispositivo
     * @param object $pedido Objeto do pedido
     * @param string $type Tipo da notificação (novo_pedido, status_update, etc)
     * @return mixed Resultado do envio ou null em caso de erro
     */
    public function sendClientePedidoNotification($deviceToken, $pedido, $type = 'novo_pedido')
    {
        try {
            $payload = $this->buildIntermediaryPayload($pedido, $type, 'cliente');

            // 🔥 Configurações para Android (com TAG para substituir)
            $androidConfig = [
                'collapseKey' => 'pedido_' . $pedido->id,
                'priority' => 'high',
                'notification' => [
                    'tag' => 'pedido_' . $pedido->id, // 🔥 SUBSTITUI a notificação
                    'sound' => $payload['notification']['sound'] ?? 'default',
                ],
            ];

            // 🔥 Configurações para iOS
            $apnsConfig = [
                'headers' => [
                    'apns-collapse-id' => 'pedido_' . $pedido->id,
                ],
                'payload' => [
                    'aps' => [
                        'sound' => $payload['notification']['sound'] ?? 'default',
                        'badge' => 1,
                    ],
                ],
            ];

            // 🔥 Se o som for null, remover para não tocar
            if ($payload['notification']['sound'] === null) {
                unset($androidConfig['notification']['sound']);
                unset($apnsConfig['payload']['aps']['sound']);
            }

            $notification = Notification::create(
                $payload['notification']['title'],
                $payload['notification']['body']
            );

            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($payload['data'])
                ->withAndroidConfig($androidConfig)
                ->withApnsConfig($apnsConfig);

            $result = $this->_messaging->send($message);
            
            Yii::info('[FIREBASE] Notificação para cliente enviada para pedido ' . $pedido->id, __METHOD__);
            return $result;
        } catch (Exception $e) {
            Yii::error('[FIREBASE] Erro ao enviar: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }
        
    /**
     * Constrói o payload intermediário para notificações de pedido
     * 
     * @param object $pedido
     * @param string $type
     * @param string $destino 'lojista' ou 'cliente'
     * @return array
     */
    private function buildIntermediaryPayload($pedido, $type, $destino = 'lojista')
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

        // Construção do corpo da notificação (diferenciado por destino)
        if ($destino === 'cliente') {
            $body = 'Seu pedido #' . ($pedido->codigo ?? $pedido->id) . ' está ' . strtolower($statusLabels[$status] ?? 'atualizado');
            // Para cliente, adiciona o nome da loja se disponível
            if (isset($pedido->loja) && is_object($pedido->loja)) {
                $body .= ' na ' . $pedido->loja->nome;
            }
        } else {
            // Para lojista
            $body = 'Cliente: ' . $clienteNome . ' - ' . $itemCount . ' itens';
        }

        // Construir ações (diferentes para cliente e lojista)
        $actions = [];
        if ($destino === 'cliente') {
            // Cliente: só ver detalhes
            $actions = [
                [
                    'id' => 'view',
                    'title' => 'Ver Pedido',
                    'type' => 'navigate',
                    'destino' => "/pedido/{$pedido->id}",
                ],
            ];
        } else {
            // Lojista: ações completas para novo pedido, ou só ver para atualizações
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
        }

        // Serializar arrays para JSON string
        $actionsJson = json_encode($actions, JSON_UNESCAPED_UNICODE);
        $extrasJson = '{}';

        // Screen padrão para cliente e lojista
        $screen = ($destino === 'cliente') ? '/pedido' : '/pedidos';

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
                'screen' => $screen,
                'destino' => $destino, // útil para o app decidir comportamento
                'actions' => $actionsJson,
                'extras' => $extrasJson,
            ],
        ];
    }
}