<?php

use yii\db\Migration;

class m260308_130945_create_categoria_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%categoria}}', [
            'id' => $this->primaryKey(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->notNull()->unique(),
            'descricao' => $this->string(255)->null(),
            
            // ========== VISUAL ==========
            'icone' => $this->string(50)->null(),
            'imagem' => $this->string(500)->null(),
            'cor' => $this->string(7)->defaultValue('#FF6B6B'),
            
            // ========== CONTROLE ==========
            'ordem' => $this->integer()->defaultValue(0),
            'ativo' => $this->boolean()->defaultValue(true),
            'destaque' => $this->boolean()->defaultValue(false),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        $this->createIndex('idx-categoria-slug', '{{%categoria}}', 'slug');
        $this->createIndex('idx-categoria-ativo', '{{%categoria}}', 'ativo');
        $this->createIndex('idx-categoria-ordem', '{{%categoria}}', 'ordem');
    }

    public function safeDown()
    {
        $this->dropTable('{{%categoria}}');
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