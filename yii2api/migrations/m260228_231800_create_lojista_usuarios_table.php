<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lojista_usuarios}}`.
 */
class m260228_231800_create_lojista_usuarios_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%lojista_usuarios}}', [
            // ========== CHAVE PRIMÁRIA ==========
            'id' => $this->primaryKey(),
            
            // ========== DADOS PESSOAIS ==========
            'nome' => $this->string(100)->notNull(),
            'email' => $this->string(150)->notNull()->unique(),
            'telefone' => $this->string(20)->null(),
            'cpf_cnpj' => $this->string(20)->null()->unique(),
            
            // ========== AUTENTICAÇÃO ==========
            'senha_hash' => $this->string(255)->notNull(), // Mudamos para NOT NULL para nosso caso
            'auth_key' => $this->string(32)->notNull(),
            'token_acesso' => $this->string(255)->null()->unique(),
            
            // ========== PERFIL E PERMISSÕES ==========
            'role' => "ENUM('admin', 'gerente', 'vendedor') NOT NULL DEFAULT 'vendedor'",
            'status' => $this->tinyInteger()->defaultValue(1), // 1 ativo, 0 inativo, 2 bloqueado
            
            // ========== METADADOS ==========
            'ultimo_login_at' => $this->timestamp()->null(),
            'ultimo_login_ip' => $this->string(45)->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // ========== ÍNDICES ==========
        $this->createIndex('idx-lojista_usuarios-email', '{{%lojista_usuarios}}', 'email');
        $this->createIndex('idx-lojista_usuarios-cpf_cnpj', '{{%lojista_usuarios}}', 'cpf_cnpj');
        $this->createIndex('idx-lojista_usuarios-token_acesso', '{{%lojista_usuarios}}', 'token_acesso');
        $this->createIndex('idx-lojista_usuarios-status', '{{%lojista_usuarios}}', 'status');
        $this->createIndex('idx-lojista_usuarios-role', '{{%lojista_usuarios}}', 'role');
    }

    public function safeDown()
    {
        $this->dropTable('{{%lojista_usuarios}}');
    }
}