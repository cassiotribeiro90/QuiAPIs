<?php

use yii\db\Migration;

class m260308_130812_create_store_usuario_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%store_usuario}}', [
            'id' => $this->primaryKey(),
            
            // ========== DADOS PESSOAIS ==========
            'nome' => $this->string(100)->notNull(),
            'email' => $this->string(150)->notNull()->unique(),
            'telefone' => $this->string(20)->null(),
            'cpf_cnpj' => $this->string(20)->null()->unique(),
            
            // ========== AUTENTICAÇÃO ==========
            'senha_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(255)->null()->unique(),
            'access_token_expira_em' => $this->timestamp()->null(),
            
            // ========== PERFIL ==========
            'funcao' => "ENUM('proprietario', 'gerente', 'vendedor') NOT NULL DEFAULT 'vendedor'",
            'status' => $this->tinyInteger()->defaultValue(1), // 1 ativo, 0 inativo, 2 bloqueado
            
            // ========== METADADOS ==========
            'ultimo_login_em' => $this->timestamp()->null(),
            'ultimo_login_ip' => $this->string(45)->null(),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deletado_em' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== ÍNDICES ==========
        $this->createIndex('idx-store_usuario-email', '{{%store_usuario}}', 'email');
        $this->createIndex('idx-store_usuario-cpf_cnpj', '{{%store_usuario}}', 'cpf_cnpj');
        $this->createIndex('idx-store_usuario-status', '{{%store_usuario}}', 'status');
        $this->createIndex('idx-store_usuario-funcao', '{{%store_usuario}}', 'funcao');
    }

    public function safeDown()
    {
        $this->dropTable('{{%store_usuario}}');
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