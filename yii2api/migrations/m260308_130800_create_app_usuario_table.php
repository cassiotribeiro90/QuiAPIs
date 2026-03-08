<?php

use yii\db\Migration;

class m260308_130800_create_app_usuario_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%app_usuario}}', [
            'id' => $this->primaryKey(),
            
            // ========== DADOS PESSOAIS ==========
            'nome' => $this->string(100)->notNull(),
            'email' => $this->string(150)->notNull()->unique(),
            'cpf' => $this->string(11)->unique(),
            'data_nascimento' => $this->date()->null(),
            
            // ========== CONTATO ==========
            'telefone' => $this->string(20)->null(),
            'telefone_verificado' => $this->boolean()->defaultValue(false),
            'whatsapp' => $this->string(20)->null(),
            
            // ========== AUTENTICAÇÃO ==========
            'senha_hash' => $this->string(255)->null(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(255)->null()->unique(),
            'access_token_expira_em' => $this->timestamp()->null(),
            'reset_token' => $this->string(255)->null()->unique(),
            'reset_token_expira_em' => $this->timestamp()->null(),
            
            // ========== LOGIN SOCIAL ==========
            'google_id' => $this->string(255)->null()->unique(),
            'facebook_id' => $this->string(255)->null()->unique(),
            'avatar' => $this->string(500)->null(),
            
            // ========== PERFIL ==========
            'tipo' => "ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente'",
            'status' => "ENUM('ativo', 'inativo', 'bloqueado', 'pendente') NOT NULL DEFAULT 'pendente'",
            
            // ========== METADADOS ==========
            'ultimo_login_em' => $this->timestamp()->null(),
            'ultimo_login_ip' => $this->string(45)->null(),
            'login_count' => $this->integer()->defaultValue(0),
            'primeiro_pedido_em' => $this->timestamp()->null(),
            'ultimo_pedido_em' => $this->timestamp()->null(),
            'total_pedidos' => $this->integer()->defaultValue(0),
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
            'pref_notificacoes_sms' => $this->boolean()->defaultValue(true),
            'pref_tema' => "ENUM('claro', 'escuro', 'auto') NOT NULL DEFAULT 'auto'",
            
            // ========== TERMOS ==========
            'email_verificado' => $this->boolean()->defaultValue(false),
            'termos_aceitos' => $this->boolean()->defaultValue(false),
            'termos_aceitos_em' => $this->timestamp()->null(),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deletado_em' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== ÍNDICES ==========
        $this->createIndex('idx-app_usuario-email', '{{%app_usuario}}', 'email');
        $this->createIndex('idx-app_usuario-cpf', '{{%app_usuario}}', 'cpf');
        $this->createIndex('idx-app_usuario-status', '{{%app_usuario}}', 'status');
        $this->createIndex('idx-app_usuario-tipo', '{{%app_usuario}}', 'tipo');
        $this->createIndex('idx-app_usuario-indicado_por', '{{%app_usuario}}', 'indicado_por');
        
        // ========== CHAVE ESTRANGEIRA (auto-relacionamento) ==========
        $this->addForeignKey(
            'fk-app_usuario-indicado_por',
            '{{%app_usuario}}',
            'indicado_por',
            '{{%app_usuario}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-app_usuario-indicado_por', '{{%app_usuario}}');
        $this->dropTable('{{%app_usuario}}');
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