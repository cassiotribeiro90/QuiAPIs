<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pedido_status_historico}}`.
 * 
 * Tabela de auditoria para histórico de mudanças de status dos pedidos
 */
class m260816_161054_create_pedido_status_historico_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pedido_status_historico}}', [
            'id' => $this->primaryKey(),
            
            // 🔥 Relacionamentos
            'pedido_id' => $this->integer()->notNull()->comment('ID do pedido (FK)'),
            'store_usuario_id' => $this->integer()->null()->comment('ID do lojista que alterou (store_usuario)'),
            'app_usuario_id' => $this->integer()->null()->comment('ID do cliente que alterou (app_usuario)'),
            
            // 🔥 Status
            'status_anterior' => $this->string(50)->null()->comment('Status antes da mudança'),
            'status_novo' => $this->string(50)->notNull()->comment('Status após a mudança'),
            
            // 🔥 Motivo (para recusas/cancelamentos)
            'motivo' => $this->text()->null()->comment('Motivo da mudança (ex: recusa, cancelamento)'),
            'motivo_codigo' => $this->string(30)->null()->comment('Código do motivo: item_indisponivel, fora_area, etc'),
            
            // 🔥 Metadados
            'ip_origem' => $this->string(45)->null()->comment('IP de onde veio a requisição'),
            'user_agent' => $this->string(255)->null()->comment('User Agent do dispositivo'),
            
            // 🔥 Timestamp
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // 🔥 Índices para otimização
        $this->createIndex('idx_pedido_status_historico_pedido', '{{%pedido_status_historico}}', 'pedido_id');
        $this->createIndex('idx_pedido_status_historico_store_usuario', '{{%pedido_status_historico}}', 'store_usuario_id');
        $this->createIndex('idx_pedido_status_historico_app_usuario', '{{%pedido_status_historico}}', 'app_usuario_id');
        $this->createIndex('idx_pedido_status_historico_criado', '{{%pedido_status_historico}}', 'criado_em');
        $this->createIndex('idx_pedido_status_historico_status_novo', '{{%pedido_status_historico}}', 'status_novo');

        // 🔥 Chaves Estrangeiras
        $this->addForeignKey(
            'fk_pedido_status_historico_pedido',
            '{{%pedido_status_historico}}',
            'pedido_id',
            '{{%pedido}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_pedido_status_historico_store_usuario',
            '{{%pedido_status_historico}}',
            'store_usuario_id',
            '{{%store_usuario}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_pedido_status_historico_app_usuario',
            '{{%pedido_status_historico}}',
            'app_usuario_id',
            '{{%app_usuario}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remover chaves estrangeiras
        $this->dropForeignKey('fk_pedido_status_historico_pedido', '{{%pedido_status_historico}}');
        $this->dropForeignKey('fk_pedido_status_historico_store_usuario', '{{%pedido_status_historico}}');
        $this->dropForeignKey('fk_pedido_status_historico_app_usuario', '{{%pedido_status_historico}}');
        
        // Remover a tabela
        $this->dropTable('{{%pedido_status_historico}}');
    }
}