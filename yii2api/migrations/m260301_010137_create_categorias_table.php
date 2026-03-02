<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%categorias}}`.
 */
class m260301_010137_create_categorias_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%categorias}}', [
            'id' => $this->primaryKey(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->unique(),
            'descricao' => $this->string(255)->null(),
            
            // ========== VISUAL ==========
            'icone' => $this->string(50)->null()->comment('código do ícone (ex: burger, drink, fries)'),
            'imagem' => $this->string(500)->null(),
            'cor' => $this->string(7)->defaultValue('#FF6B6B')->comment('Cor principal da categoria'),
            
            // ========== CONTROLE ==========
            'ordem' => $this->integer()->defaultValue(0),
            'ativo' => $this->boolean()->defaultValue(true),
            'destaque' => $this->boolean()->defaultValue(false)->comment('aparece na home?'),
            
            // ========== METADADOS ==========
            'metadata' => $this->json()->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-categorias-ativo', '{{%categorias}}', 'ativo');
        $this->createIndex('idx-categorias-ordem', '{{%categorias}}', 'ordem');
        $this->createIndex('idx-categorias-destaque', '{{%categorias}}', 'destaque');
    }

    public function safeDown()
    {
        $this->dropTable('{{%categorias}}');
    }
}
