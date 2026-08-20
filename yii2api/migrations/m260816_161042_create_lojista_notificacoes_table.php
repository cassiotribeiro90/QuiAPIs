<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lojista_notificacoes}}`.
 * 
 * Tabela de configurações de notificações por dispositivo do lojista
 */
class m260816_161042_create_lojista_notificacoes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lojista_notificacoes}}', [
            'id' => $this->primaryKey(),
            
            // 🔥 Relacionamentos
            'lojista_id' => $this->integer()->notNull()->comment('ID do lojista (store_usuario)'),
            
            // 🔥 Dispositivo
            'device_id' => $this->string(255)->notNull()->comment('Identificador único do dispositivo'),
            'fcm_token' => $this->string(255)->null()->comment('Token do Firebase Cloud Messaging'),
            'platform' => "ENUM('android', 'ios', 'web') NOT NULL DEFAULT 'android' COMMENT 'Plataforma do dispositivo'",
            'app_version' => $this->string(20)->null()->comment('Versão do app'),
            
            // 🔥 Configurações de notificação
            'notificacoes_ativas' => $this->boolean()->defaultValue(1)->comment('Notificações push ativas'),
            'som_personalizado' => $this->string(100)->null()->comment('Nome do som personalizado'),
            
            // 🔥 Timestamps
            'ultimo_acesso_em' => $this->timestamp()->null()->comment('Último uso do dispositivo'),
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // 🔥 Índices para otimização
        $this->createIndex('idx_lojista_notificacoes_lojista', '{{%lojista_notificacoes}}', 'lojista_id');
        $this->createIndex('idx_lojista_notificacoes_device', '{{%lojista_notificacoes}}', 'device_id', true);
        $this->createIndex('idx_lojista_notificacoes_fcm', '{{%lojista_notificacoes}}', 'fcm_token');
        $this->createIndex('idx_lojista_notificacoes_lojista_device', '{{%lojista_notificacoes}}', ['lojista_id', 'device_id'], true);

        // 🔥 Chave Estrangeira
        $this->addForeignKey(
            'fk_lojista_notificacoes_lojista',
            '{{%lojista_notificacoes}}',
            'lojista_id',
            '{{%store_usuario}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remover chave estrangeira
        $this->dropForeignKey('fk_lojista_notificacoes_lojista', '{{%lojista_notificacoes}}');
        
        // Remover a tabela
        $this->dropTable('{{%lojista_notificacoes}}');
    }
}