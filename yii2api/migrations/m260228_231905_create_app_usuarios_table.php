<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%app_usuarios}}`.
 */
class m260228_231905_create_app_usuarios_table extends Migration
{
        public function safeUp()
    {
        $this->createTable('{{%app_usuarios}}', [
            // ========== CHAVE PRIMÁRIA ==========
            'id' => $this->primaryKey(),
            
            // ========== DADOS PESSOAIS ==========
            'nome' => $this->string(100)->notNull(),
            'email' => $this->string(150)->notNull()->unique(),
            'cpf' => $this->string(11)->unique(),
            'data_nascimento' => $this->date()->null(),
            
            // ========== CONTATO ==========
            'telefone' => $this->string(20)->null(),
            'telefone_verified' => $this->boolean()->defaultValue(false),
            'whatsapp' => $this->string(20)->null(),
            
            // ========== AUTENTICAÇÃO ==========
            'senha_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(255)->null()->unique(),
            'access_token_expires_at' => $this->timestamp()->null(),
            'password_reset_token' => $this->string(255)->null()->unique(),
            'password_reset_expires_at' => $this->timestamp()->null(),
            
            // ========== PERFIL ==========
            'tipo' => "ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente'",
            'status' => "ENUM('ativo', 'inativo', 'bloqueado', 'pendente') NOT NULL DEFAULT 'pendente'",
            
            // ========== METADADOS DE ACESSO ==========
            'ultimo_login_at' => $this->timestamp()->null(),
            'ultimo_login_ip' => $this->string(45)->null(),
            'login_count' => $this->integer()->defaultValue(0),
            
            // ========== MÉTRICAS DE NEGÓCIO ==========
            'primeira_compra_at' => $this->timestamp()->null(),
            'ultima_compra_at' => $this->timestamp()->null(),
            'total_compras' => $this->integer()->defaultValue(0),
            'total_gasto' => $this->decimal(10,2)->defaultValue(0),
            
            // ========== FIDELIDADE ==========
            'pontos' => $this->integer()->defaultValue(0),
            'nivel' => $this->integer()->defaultValue(1),
            'indicado_por' => $this->integer()->null(),
            'codigo_indicacao' => $this->string(20)->null()->unique(),
            'indicacoes_count' => $this->integer()->defaultValue(0),
            
            // ========== PREFERÊNCIAS ==========
            'pref_notificacoes_email' => $this->boolean()->defaultValue(true),
            'pref_notificacoes_push' => $this->boolean()->defaultValue(true),
            'pref_notificacoes_sms' => $this->boolean()->defaultValue(true), // AGORA TRUE (agressivo)
            'pref_tema' => "ENUM('light', 'dark', 'auto') NOT NULL DEFAULT 'auto'",
            
            // ========== TERMOS ==========
            'email_verified' => $this->boolean()->defaultValue(false),
            'termos_aceitos' => $this->boolean()->defaultValue(false),
            'termos_aceitos_at' => $this->timestamp()->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== ÍNDICES ==========
        $this->createIndex('idx-app_usuarios-email', '{{%app_usuarios}}', 'email');
        $this->createIndex('idx-app_usuarios-cpf', '{{%app_usuarios}}', 'cpf');
        $this->createIndex('idx-app_usuarios-status', '{{%app_usuarios}}', 'status');
        $this->createIndex('idx-app_usuarios-tipo', '{{%app_usuarios}}', 'tipo');
        $this->createIndex('idx-app_usuarios-access_token', '{{%app_usuarios}}', 'access_token');
        $this->createIndex('idx-app_usuarios-ultimo_login_at', '{{%app_usuarios}}', 'ultimo_login_at');
        $this->createIndex('idx-app_usuarios-indicado_por', '{{%app_usuarios}}', 'indicado_por');
        $this->createIndex('idx-app_usuarios-pontos', '{{%app_usuarios}}', 'pontos');
        $this->createIndex('idx-app_usuarios-nivel', '{{%app_usuarios}}', 'nivel');
        
        // ========== CHAVE ESTRANGEIRA (auto-relacionamento) ==========
        $this->addForeignKey(
            'fk-app_usuarios-indicado_por',
            '{{%app_usuarios}}',
            'indicado_por',
            '{{%app_usuarios}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        
        // ========== COMENTÁRIO DA TABELA ==========
        $this->addCommentOnTable('{{%app_usuarios}}', 'Usuários do aplicativo (clientes) - MVP 1.0 - Sem foto, SMS ativo');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-app_usuarios-indicado_por', '{{%app_usuarios}}');
        $this->dropTable('{{%app_usuarios}}');
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
