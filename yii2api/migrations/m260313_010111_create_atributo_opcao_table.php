<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%atributo_opcao}}`.
 */
class m260313_010111_create_atributo_opcao_table extends Migration
{
    public function safeUp()
    {
        // 🔥 TABLE OPTIONS DEFINIDAS AQUI
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%atributo_opcao}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTO ==========
            'categoria_id' => $this->integer()->notNull(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(100)->notNull(),
            'descricao' => $this->string(255)->null(),
            
            // ========== PREÇO ==========
            'preco_adicional' => $this->decimal(10,2)->defaultValue(0),
            
            // ========== VISUAL ==========
            'icone' => $this->string(50)->null(),
            'imagem' => $this->string(500)->null(),
            'cor' => $this->string(7)->null(),
            
            // ========== DISPONIBILIDADE ==========
            'disponivel' => $this->boolean()->defaultValue(true),
            'estoque' => $this->integer()->defaultValue(0),
            
            // ========== CONTROLE ==========
            'ordem' => $this->integer()->defaultValue(0),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-atributo_opcao-categoria_id',
            '{{%atributo_opcao}}',
            'categoria_id',
            '{{%atributo_categoria}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-atributo_opcao-categoria_id', '{{%atributo_opcao}}', 'categoria_id');
        $this->createIndex('idx-atributo_opcao-disponivel', '{{%atributo_opcao}}', 'disponivel');
        $this->createIndex('idx-atributo_opcao-ordem', '{{%atributo_opcao}}', 'ordem');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-atributo_opcao-categoria_id', '{{%atributo_opcao}}');
        $this->dropTable('{{%atributo_opcao}}');
    }
}