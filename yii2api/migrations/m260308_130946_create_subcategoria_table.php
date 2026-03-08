<?php

use yii\db\Migration;

class m260308_130946_create_subcategoria_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%subcategoria}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTO ==========
            'categoria_id' => $this->integer()->notNull(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->notNull()->unique(),
            'descricao' => $this->string(255)->null(),
            
            // ========== VISUAL ==========
            'icone' => $this->string(50)->null(),
            'imagem' => $this->string(500)->null(),
            
            // ========== CONTROLE ==========
            'ordem' => $this->integer()->defaultValue(0),
            'ativo' => $this->boolean()->defaultValue(true),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-subcategoria-categoria_id',
            '{{%subcategoria}}',
            'categoria_id',
            '{{%categoria}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-subcategoria-slug', '{{%subcategoria}}', 'slug');
        $this->createIndex('idx-subcategoria-categoria_id', '{{%subcategoria}}', 'categoria_id');
        $this->createIndex('idx-subcategoria-ativo', '{{%subcategoria}}', 'ativo');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-subcategoria-categoria_id', '{{%subcategoria}}');
        $this->dropTable('{{%subcategoria}}');
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