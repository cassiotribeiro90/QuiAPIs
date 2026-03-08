<?php

use yii\db\Migration;

class m260308_132753_create_avaliacao_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%avaliacao}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'usuario_id' => $this->integer()->notNull(),
            'loja_id' => $this->integer()->notNull(),
            'pedido_id' => $this->integer()->null(),
            'produto_id' => $this->integer()->null(),
            
            // ========== AVALIAÇÃO ==========
            'nota' => $this->integer()->notNull()->check('nota BETWEEN 1 AND 5'),
            'comentario' => $this->text()->null(),
            
            // ========== RESPOSTA DA LOJA ==========
            'resposta' => $this->text()->null(),
            'resposta_em' => $this->timestamp()->null(),
            
            // ========== METADADOS ==========
            'fotos' => $this->json()->null(),
            'curtidas' => $this->integer()->defaultValue(0),
            
            // ========== STATUS ==========
            'status' => "ENUM('pendente', 'aprovado', 'rejeitado') NOT NULL DEFAULT 'aprovado'",
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deletado_em' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-avaliacao-usuario_id',
            '{{%avaliacao}}',
            'usuario_id',
            '{{%app_usuario}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-avaliacao-loja_id',
            '{{%avaliacao}}',
            'loja_id',
            '{{%loja}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-avaliacao-pedido_id',
            '{{%avaliacao}}',
            'pedido_id',
            '{{%pedido}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-avaliacao-produto_id',
            '{{%avaliacao}}',
            'produto_id',
            '{{%produto}}',
            'id',
            'SET NULL'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-avaliacao-usuario_id', '{{%avaliacao}}', 'usuario_id');
        $this->createIndex('idx-avaliacao-loja_id', '{{%avaliacao}}', 'loja_id');
        $this->createIndex('idx-avaliacao-pedido_id', '{{%avaliacao}}', 'pedido_id');
        $this->createIndex('idx-avaliacao-produto_id', '{{%avaliacao}}', 'produto_id');
        $this->createIndex('idx-avaliacao-nota', '{{%avaliacao}}', 'nota');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-avaliacao-produto_id', '{{%avaliacao}}');
        $this->dropForeignKey('fk-avaliacao-pedido_id', '{{%avaliacao}}');
        $this->dropForeignKey('fk-avaliacao-loja_id', '{{%avaliacao}}');
        $this->dropForeignKey('fk-avaliacao-usuario_id', '{{%avaliacao}}');
        $this->dropTable('{{%avaliacao}}');
    }
    
    private function getTableOptions()
    {
        $driver = $this->db->driverName;
        if ($driver === 'mysql') {
            return 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        return null;
    }
}