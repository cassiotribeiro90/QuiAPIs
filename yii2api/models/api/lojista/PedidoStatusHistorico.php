<?php

namespace app\models\api\lojista;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pedido_status_historico".
 *
 * @property int $id
 * @property int $pedido_id
 * @property int|null $store_usuario_id
 * @property int|null $app_usuario_id
 * @property string|null $status_anterior
 * @property string $status_novo
 * @property string|null $motivo
 * @property string|null $motivo_codigo
 * @property string|null $ip_origem
 * @property string|null $user_agent
 * @property string $criado_em
 */
class PedidoStatusHistorico extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%pedido_status_historico}}';
    }

    public function rules()
    {
        return [
            [['pedido_id', 'status_novo'], 'required'],
            [['pedido_id', 'store_usuario_id', 'app_usuario_id'], 'integer'],
            [['status_anterior', 'status_novo', 'motivo', 'motivo_codigo', 'ip_origem', 'user_agent'], 'string'],
            [['criado_em'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_id' => 'Pedido',
            'store_usuario_id' => 'Lojista',
            'app_usuario_id' => 'Cliente',
            'status_anterior' => 'Status Anterior',
            'status_novo' => 'Status Novo',
            'motivo' => 'Motivo',
            'motivo_codigo' => 'Código do Motivo',
            'ip_origem' => 'IP de Origem',
            'user_agent' => 'User Agent',
            'criado_em' => 'Criado em',
        ];
    }

    // ==================== RELACIONAMENTOS ====================

    public function getPedido()
    {
        return $this->hasOne(\app\models\api\app\Pedido::class, ['id' => 'pedido_id']);
    }

    public function getStoreUsuario()
    {
        return $this->hasOne(\app\models\api\lojista\LojistaUsuario::class, ['id' => 'store_usuario_id']);
    }

    public function getAppUsuario()
    {
        return $this->hasOne(\app\models\api\app\Usuario::class, ['id' => 'app_usuario_id']);
    }

    // ==================== MÉTODOS AUXILIARES ====================

    public function getResponsavelNome()
    {
        if ($this->store_usuario_id) {
            $lojista = \app\models\api\lojista\LojistaUsuario::findOne($this->store_usuario_id);
            return $lojista ? $lojista->nome : 'Lojista (removido)';
        }
        if ($this->app_usuario_id) {
            $cliente = \app\models\api\app\Usuario::findOne($this->app_usuario_id);
            return $cliente ? $cliente->nome : 'Cliente (removido)';
        }
        return 'Sistema';
    }

    public function getStatusLabel($status)
    {
        $labels = [
            'novo' => 'Novo',
            'aguardando' => 'Aguardando',
            'confirmado' => 'Confirmado',
            'em_preparo' => 'Em preparo',
            'pronto' => 'Pronto',
            'saiu' => 'Saiu para entrega',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
            'recusado' => 'Recusado',
        ];
        return $labels[$status] ?? $status;
    }

    public static function registrar($pedidoId, $statusNovo, $statusAnterior = null, $storeUsuarioId = null, $appUsuarioId = null, $motivo = null, $motivoCodigo = null)
    {
        $model = new static();
        $model->pedido_id = $pedidoId;
        $model->status_novo = $statusNovo;
        $model->status_anterior = $statusAnterior;
        $model->store_usuario_id = $storeUsuarioId;
        $model->app_usuario_id = $appUsuarioId;
        $model->motivo = $motivo;
        $model->motivo_codigo = $motivoCodigo;
        $model->ip_origem = Yii::$app->request->userIP;
        $model->user_agent = Yii::$app->request->userAgent;
        
        return $model->save();
    }
}