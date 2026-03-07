<?php
// Arquivo: migrations/m[timestamp]_create_gestor_usuarios_table.php

use yii\db\Migration;

class m260228_232114_create_gestor_usuarios_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%gestor_usuarios}}', [
            'id' => $this->primaryKey(),
            
            // ========== DADOS PESSOAIS ==========
            'nome' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull()->unique(),
            'cpf' => $this->string(14)->null(),
            'telefone' => $this->string(15)->null(),
            
            // ========== AUTENTICAÇÃO ==========
            'senha_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(255)->null(),
            'access_token_expires_at' => $this->datetime()->null(),
            
            // ========== PERFIL ==========
            'tipo' => "ENUM('comercial', 'admin', 'suporte') NOT NULL DEFAULT 'comercial'",
            'status' => $this->tinyInteger()->defaultValue(1), // 1 ativo, 0 inativo, 2 bloqueado
            
            // ========== METADADOS ==========
            'ultimo_login_at' => $this->datetime()->null(),
            'ultimo_login_ip' => $this->string(45)->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ]);

        // ========== ÍNDICES ==========
        $this->createIndex('idx-gestor_usuarios-email', '{{%gestor_usuarios}}', 'email');
        $this->createIndex('idx-gestor_usuarios-cpf', '{{%gestor_usuarios}}', 'cpf');
        $this->createIndex('idx-gestor_usuarios-token', '{{%gestor_usuarios}}', 'access_token');
        $this->createIndex('idx-gestor_usuarios-status', '{{%gestor_usuarios}}', 'status');
        $this->createIndex('idx-gestor_usuarios-tipo', '{{%gestor_usuarios}}', 'tipo');
    }

    public function safeDown()
    {
        $this->dropTable('{{%gestor_usuarios}}');
    }
}