<?php

use yii\db\Migration;

class m260308_132711_create_pedido_item_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%pedido_item}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'pedido_id' => $this->integer()->notNull(),
            'produto_id' => $this->integer()->notNull(),
            
            // ========== QUANTIDADE ==========
            'quantidade' => $this->integer()->notNull()->defaultValue(1),
            
            // ========== PREÇOS (SNAPSHOT) ==========
            'preco_unitario' => $this->decimal(10,2)->notNull(),
            'preco_total' => $this->decimal(10,2)->notNull(),
            
            // ========== SNAPSHOT DO PRODUTO ==========
            'produto_nome' => $this->string(255)->notNull(),
            'produto_descricao' => $this->text()->null(),
            'produto_imagem' => $this->string(500)->null(),
            
            // ========== OPÇÕES ESCOLHIDAS ==========
            'opcoes' => $this->json()->null(),
            'observacao' => $this->text()->null(),
            
            // ========== AVALIAÇÃO DO ITEM ==========
            'avaliacao_nota' => $this->integer()->null(),
            'avaliacao_comentario' => $this->text()->null(),
            
            // ========== METADADOS ==========
            'metadata' => $this->json()->null(),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-pedido_item-pedido_id',
            '{{%pedido_item}}',
            'pedido_id',
            '{{%pedido}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-pedido_item-produto_id',
            '{{%pedido_item}}',
            'produto_id',
            '{{%produto}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-pedido_item-pedido_id', '{{%pedido_item}}', 'pedido_id');
        $this->createIndex('idx-pedido_item-produto_id', '{{%pedido_item}}', 'produto_id');
        $this->createIndex('idx-pedido_item-avaliacao_nota', '{{%pedido_item}}', 'avaliacao_nota');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-pedido_item-produto_id', '{{%pedido_item}}');
        $this->dropForeignKey('fk-pedido_item-pedido_id', '{{%pedido_item}}');
        $this->dropTable('{{%pedido_item}}');
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