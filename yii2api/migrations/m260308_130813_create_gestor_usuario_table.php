<?php

use yii\db\Migration;

class m260308_130813_create_gestor_usuario_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%gestor_usuario}}', [
            'id' => $this->primaryKey(),
            
            // ========== DADOS PESSOAIS ==========
            'nome' => $this->string(100)->notNull(),
            'email' => $this->string(150)->notNull()->unique(),
            'cpf' => $this->string(11)->null()->unique(),
            'telefone' => $this->string(20)->null(),
            
            // ========== AUTENTICAÇÃO ==========
            'senha_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(255)->null()->unique(),
            'access_token_expira_em' => $this->timestamp()->null(),
            
            // ========== PERFIL ==========
            'nivel' => "ENUM('comercial', 'admin', 'suporte', 'financeiro') NOT NULL DEFAULT 'comercial'",
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
        $this->createIndex('idx-gestor_usuario-email', '{{%gestor_usuario}}', 'email');
        $this->createIndex('idx-gestor_usuario-cpf', '{{%gestor_usuario}}', 'cpf');
        $this->createIndex('idx-gestor_usuario-status', '{{%gestor_usuario}}', 'status');
        $this->createIndex('idx-gestor_usuario-nivel', '{{%gestor_usuario}}', 'nivel');
    }

    public function safeDown()
    {
        $this->dropTable('{{%gestor_usuario}}');
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