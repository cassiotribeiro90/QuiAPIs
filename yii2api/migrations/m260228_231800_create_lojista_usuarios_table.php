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
            
            // ========== AUTENTICAÇÃO ==========
            'senha_hash' => $this->string(255)->null(), // NULL para usuários sociais
            'auth_key' => $this->string(32)->notNull(),
            'token_acesso' => $this->string(255)->null()->unique(),
            
            // ========== CAMPOS SOCIAIS ==========
            'google_id' => $this->string(255)->null()->unique(),
            'facebook_id' => $this->string(255)->null()->unique(),
            'avatar' => $this->string(500)->null(),
            
            // ========== VERIFICAÇÃO ==========
            'email_verified' => $this->boolean()->defaultValue(false),
            
            // ========== STATUS ==========
            'status' => $this->tinyInteger()->defaultValue(10), // 10 ativo, 0 inativo
            
            // ========== METADADOS DE ACESSO ==========
            'ultimo_login_at' => $this->timestamp()->null(),
            'ultimo_login_ip' => $this->string(45)->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        // ========== ÍNDICES ==========
        $this->createIndex('idx-lojista_usuarios-email', '{{%lojista_usuarios}}', 'email');
        $this->createIndex('idx-lojista_usuarios-google_id', '{{%lojista_usuarios}}', 'google_id');
        $this->createIndex('idx-lojista_usuarios-facebook_id', '{{%lojista_usuarios}}', 'facebook_id');
        $this->createIndex('idx-lojista_usuarios-token_acesso', '{{%lojista_usuarios}}', 'token_acesso');
        $this->createIndex('idx-lojista_usuarios-status', '{{%lojista_usuarios}}', 'status');
        
        // ========== COMENTÁRIO DA TABELA ==========
        $this->addCommentOnTable('{{%lojista_usuarios}}', 'Usuários do painel lojista com suporte a login social');
    }

    public function safeDown()
    {
        $this->dropTable('{{%lojista_usuarios}}');
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