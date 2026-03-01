<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subcategorias}}`.
 */
class m260301_010137_create_subcategorias_table extends Migration
{
        public function safeUp()
    {
        $this->createTable('{{%subcategorias}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTO ==========
            'categoria_id' => $this->integer()->notNull(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->unique(),
            'descricao' => $this->string(255)->null(),
            
            // ========== VISUAL ==========
            'icone' => $this->string(50)->null(),
            'imagem' => $this->string(500)->null(),
            
            // ========== CONTROLE ==========
            'ordem' => $this->integer()->defaultValue(0),
            'ativo' => $this->boolean()->defaultValue(true),
            
            // ========== METADADOS ==========
            'metadata' => $this->json()->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-subcategorias-categoria_id',
            '{{%subcategorias}}',
            'categoria_id',
            '{{%categorias}}',
            'id',
            'CASCADE'
        );

        $this->createIndex('idx-subcategorias-categoria_id', '{{%subcategorias}}', 'categoria_id');
        $this->createIndex('idx-subcategorias-ativo', '{{%subcategorias}}', 'ativo');
        $this->createIndex('idx-subcategorias-ordem', '{{%subcategorias}}', 'ordem');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-subcategorias-categoria_id', '{{%subcategorias}}');
        $this->dropTable('{{%subcategorias}}');
    }
}
