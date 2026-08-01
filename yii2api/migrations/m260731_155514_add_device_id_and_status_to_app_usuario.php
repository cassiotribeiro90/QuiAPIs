<?php

use yii\db\Migration;

class m260731_155514_add_device_id_and_status_to_app_usuario extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->getTableSchema('app_usuario');

        // Adiciona coluna device_id se não existir
        if (!$schema->getColumn('device_id')) {
            $this->addColumn('app_usuario', 'device_id', $this->string(255)->null());
            $this->createIndex('idx_app_usuario_device_id', 'app_usuario', 'device_id');
        }

        // Adiciona coluna access_token se não existir
        if (!$schema->getColumn('access_token')) {
            $this->addColumn('app_usuario', 'access_token', $this->string(255)->null());
            $this->createIndex('idx_app_usuario_access_token', 'app_usuario', 'access_token', true);
        }
    }

    public function safeDown()
    {
        $schema = $this->db->getTableSchema('app_usuario');

        if ($schema->getColumn('device_id')) {
            $this->dropIndex('idx_app_usuario_device_id', 'app_usuario');
            $this->dropColumn('app_usuario', 'device_id');
        }

        if ($schema->getColumn('access_token')) {
            $this->dropIndex('idx_app_usuario_access_token', 'app_usuario');
            $this->dropColumn('app_usuario', 'access_token');
        }
    }
}