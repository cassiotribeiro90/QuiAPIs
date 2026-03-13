<?php

use yii\db\Migration;

class m260313_010150_create_produto_opcao_adicional_table extends Migration
{
    public function safeUp()
    {
        // 🔥 TABLE OPTIONS DEFINIDAS AQUI
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%produto_opcao_adicional}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'produto_id' => $this->integer()->notNull(),
            'opcao_id' => $this->integer()->notNull(),
            
            // ========== PREÇO ESPECÍFICO ==========
            'preco_adicional' => $this->decimal(10,2)->null(),
            
            // ========== DISPONIBILIDADE ==========
            'disponivel' => $this->boolean()->defaultValue(true),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-produto_opcao_adicional-produto_id',
            '{{%produto_opcao_adicional}}',
            'produto_id',
            '{{%produto}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-produto_opcao_adicional-opcao_id',
            '{{%produto_opcao_adicional}}',
            'opcao_id',
            '{{%atributo_opcao}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-produto_opcao_adicional-unico', '{{%produto_opcao_adicional}}', ['produto_id', 'opcao_id'], true);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-produto_opcao_adicional-opcao_id', '{{%produto_opcao_adicional}}');
        $this->dropForeignKey('fk-produto_opcao_adicional-produto_id', '{{%produto_opcao_adicional}}');
        $this->dropTable('{{%produto_opcao_adicional}}');
    }
}