<?php

use yii\db\Migration;

class m260308_131010_create_store_usuario_loja_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%store_usuario_loja}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'usuario_id' => $this->integer()->notNull(),
            'loja_id' => $this->integer()->notNull(),
            
            // ========== PERFIL NA LOJA ==========
            'funcao' => "ENUM('proprietario', 'gerente', 'vendedor') NOT NULL DEFAULT 'vendedor'",
            'status' => $this->tinyInteger()->defaultValue(1), // 1 ativo, 0 inativo
            
            // ========== PERMISSÕES ESPECÍFICAS ==========
            'permissoes' => $this->json()->null(),
            
            // ========== METADADOS ==========
            'ultimo_acesso_em' => $this->timestamp()->null(),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-store_usuario_loja-usuario_id',
            '{{%store_usuario_loja}}',
            'usuario_id',
            '{{%store_usuario}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-store_usuario_loja-loja_id',
            '{{%store_usuario_loja}}',
            'loja_id',
            '{{%loja}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-store_usuario_loja-usuario_id', '{{%store_usuario_loja}}', 'usuario_id');
        $this->createIndex('idx-store_usuario_loja-loja_id', '{{%store_usuario_loja}}', 'loja_id');
        $this->createIndex('idx-store_usuario_loja-unico', '{{%store_usuario_loja}}', ['usuario_id', 'loja_id'], true);
        $this->createIndex('idx-store_usuario_loja-funcao', '{{%store_usuario_loja}}', 'funcao');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-store_usuario_loja-loja_id', '{{%store_usuario_loja}}');
        $this->dropForeignKey('fk-store_usuario_loja-usuario_id', '{{%store_usuario_loja}}');
        $this->dropTable('{{%store_usuario_loja}}');
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