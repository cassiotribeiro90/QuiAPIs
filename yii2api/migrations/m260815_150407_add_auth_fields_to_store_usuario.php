<?php

use yii\db\Migration;

class m260815_150407_add_auth_fields_to_store_usuario extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 🔥 1. Adicionar colunas para OTP (reset token)
        $this->addColumn('store_usuario', 'reset_token', $this->string(255)->null()->after('deletado_em'));
        $this->addColumn('store_usuario', 'reset_token_expira_em', $this->timestamp()->null()->after('reset_token'));
        
        // 🔥 2. Adicionar colunas para Refresh Token
        $this->addColumn('store_usuario', 'refresh_token', $this->string(255)->null()->after('access_token_expira_em'));
        $this->addColumn('store_usuario', 'refresh_token_expira_em', $this->timestamp()->null()->after('refresh_token'));
        
        // 🔥 3. Adicionar coluna para Device ID
        $this->addColumn('store_usuario', 'device_id', $this->string(255)->null()->after('refresh_token_expira_em'));
        
        // 🔥 4. Adicionar coluna para contador de login
        $this->addColumn('store_usuario', 'login_count', $this->integer()->defaultValue(0)->after('device_id'));
        
        // 🔥 5. Adicionar coluna para provedor do último login
        $this->addColumn('store_usuario', 'ultimo_login_provider', $this->string(50)->null()->after('login_count'));
        
        // 🔥 6. Adicionar índices para otimização
        $this->createIndex('idx_store_usuario_refresh_token', 'store_usuario', 'refresh_token');
        $this->createIndex('idx_store_usuario_device_id', 'store_usuario', 'device_id');
        $this->createIndex('idx_store_usuario_reset_token', 'store_usuario', 'reset_token');
        
        // 🔥 7. Adicionar índices compostos para expiração
        $this->createIndex('idx_store_usuario_refresh_token_expira', 'store_usuario', ['refresh_token', 'refresh_token_expira_em']);
        $this->createIndex('idx_store_usuario_access_token_expira', 'store_usuario', ['access_token', 'access_token_expira_em']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remover índices
        $this->dropIndex('idx_store_usuario_refresh_token', 'store_usuario');
        $this->dropIndex('idx_store_usuario_device_id', 'store_usuario');
        $this->dropIndex('idx_store_usuario_reset_token', 'store_usuario');
        $this->dropIndex('idx_store_usuario_refresh_token_expira', 'store_usuario');
        $this->dropIndex('idx_store_usuario_access_token_expira', 'store_usuario');
        
        // Remover colunas
        $this->dropColumn('store_usuario', 'reset_token');
        $this->dropColumn('store_usuario', 'reset_token_expira_em');
        $this->dropColumn('store_usuario', 'refresh_token');
        $this->dropColumn('store_usuario', 'refresh_token_expira_em');
        $this->dropColumn('store_usuario', 'device_id');
        $this->dropColumn('store_usuario', 'login_count');
        $this->dropColumn('store_usuario', 'ultimo_login_provider');
    }
}