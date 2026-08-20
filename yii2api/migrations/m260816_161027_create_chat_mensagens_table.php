<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chat_mensagens}}`.
 * 
 * Tabela de mensagens do chat entre lojista e cliente
 */
class m260816_161027_create_chat_mensagens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chat_mensagens}}', [
            'id' => $this->primaryKey(),
            
            // 🔥 Relacionamentos
            'pedido_id' => $this->integer()->notNull()->comment('ID do pedido (FK)'),
            'lojista_id' => $this->integer()->notNull()->comment('ID do lojista que enviou (store_usuario)'),
            'cliente_id' => $this->integer()->notNull()->comment('ID do cliente que enviou (app_usuario)'),
            
            // 🔥 Conteúdo da mensagem
            'mensagem' => $this->text()->notNull()->comment('Conteúdo da mensagem'),
            'tipo' => "ENUM('texto', 'imagem', 'audio', 'sistema') NOT NULL DEFAULT 'texto' COMMENT 'Tipo da mensagem'",
            'anexo_url' => $this->string(500)->null()->comment('URL do anexo (imagem/áudio/documento)'),
            
            // 🔥 Status da mensagem
            'lida' => $this->boolean()->defaultValue(0)->comment('0 = não lida, 1 = lida'),
            'lida_em' => $this->timestamp()->null()->comment('Data/Hora da leitura'),
            
            // 🔥 Quem enviou
            'enviado_por' => "ENUM('lojista', 'cliente', 'sistema') NOT NULL COMMENT 'Quem enviou a mensagem'",
            
            // 🔥 Timestamps
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // 🔥 Índices para otimização de consultas
        $this->createIndex('idx_chat_mensagens_pedido', '{{%chat_mensagens}}', 'pedido_id');
        $this->createIndex('idx_chat_mensagens_lojista', '{{%chat_mensagens}}', 'lojista_id');
        $this->createIndex('idx_chat_mensagens_cliente', '{{%chat_mensagens}}', 'cliente_id');
        $this->createIndex('idx_chat_mensagens_criado', '{{%chat_mensagens}}', 'criado_em');
        $this->createIndex('idx_chat_mensagens_pedido_lida', '{{%chat_mensagens}}', ['pedido_id', 'lida']);

        // 🔥 Chaves Estrangeiras
        $this->addForeignKey(
            'fk_chat_mensagens_pedido',
            '{{%chat_mensagens}}',
            'pedido_id',
            '{{%pedido}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_chat_mensagens_lojista',
            '{{%chat_mensagens}}',
            'lojista_id',
            '{{%store_usuario}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // 🔥 CORREÇÃO: Cliente usa app_usuario
        $this->addForeignKey(
            'fk_chat_mensagens_cliente',
            '{{%chat_mensagens}}',
            'cliente_id',
            '{{%app_usuario}}',  // ← CORRETO: app_usuario
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
        // Remover chaves estrangeiras
        $this->dropForeignKey('fk_chat_mensagens_pedido', '{{%chat_mensagens}}');
        $this->dropForeignKey('fk_chat_mensagens_lojista', '{{%chat_mensagens}}');
        $this->dropForeignKey('fk_chat_mensagens_cliente', '{{%chat_mensagens}}');
        
        // Remover a tabela
        $this->dropTable('{{%chat_mensagens}}');
    }
}