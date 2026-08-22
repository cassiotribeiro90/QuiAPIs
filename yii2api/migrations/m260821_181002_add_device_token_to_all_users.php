<?php

use yii\db\Migration;

class m260821_181002_add_device_token_to_all_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 🔥 1. Tabela app_usuario (clientes)
        $this->addColumn('app_usuario', 'device_token', $this->string(255)->null()->after('telefone_verificado'));
        $this->createIndex('idx_app_usuario_device_token', 'app_usuario', 'device_token');

        // 🔥 2. Tabela store_usuario (lojistas)
        $this->addColumn('store_usuario', 'device_token', $this->string(255)->null()->after('device_id'));
        $this->createIndex('idx_store_usuario_device_token', 'store_usuario', 'device_token');

        // 🔥 3. Tabela gestor_usuario (gestores)
        $this->addColumn('gestor_usuario', 'device_token', $this->string(255)->null()->after('status'));
        $this->createIndex('idx_gestor_usuario_device_token', 'gestor_usuario', 'device_token');

        echo "✅ Colunas 'device_token' adicionadas com sucesso!\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // 🔥 Remove os índices e colunas
        $this->dropIndex('idx_app_usuario_device_token', 'app_usuario');
        $this->dropColumn('app_usuario', 'device_token');

        $this->dropIndex('idx_store_usuario_device_token', 'store_usuario');
        $this->dropColumn('store_usuario', 'device_token');

        $this->dropIndex('idx_gestor_usuario_device_token', 'gestor_usuario');
        $this->dropColumn('gestor_usuario', 'device_token');

        echo "✅ Colunas 'device_token' removidas com sucesso!\n";

        return true;
    }
}