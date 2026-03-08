<?php

use yii\db\Migration;

class m260308_132832_create_loja_destaque_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%loja_destaque}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTO ==========
            'loja_id' => $this->integer()->notNull(),
            
            // ========== CONTROLE ==========
            'titulo' => $this->string(255)->notNull(),
            'descricao' => $this->string(255)->null(),
            'imagem' => $this->string(500)->null(),
            'ordem' => $this->integer()->defaultValue(0),
            
            // ========== PERÍODO ==========
            'data_inicio' => $this->date()->null(),
            'data_fim' => $this->date()->null(),
            
            // ========== STATUS ==========
            'ativo' => $this->boolean()->defaultValue(true),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-loja_destaque-loja_id',
            '{{%loja_destaque}}',
            'loja_id',
            '{{%loja}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-loja_destaque-loja_id', '{{%loja_destaque}}', 'loja_id');
        $this->createIndex('idx-loja_destaque-ativo', '{{%loja_destaque}}', 'ativo');
        $this->createIndex('idx-loja_destaque-ordem', '{{%loja_destaque}}', 'ordem');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-loja_destaque-loja_id', '{{%loja_destaque}}');
        $this->dropTable('{{%loja_destaque}}');
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