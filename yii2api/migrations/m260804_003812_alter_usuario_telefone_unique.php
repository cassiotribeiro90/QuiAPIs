<?php

use yii\db\Migration;

class m260804_003812_alter_usuario_telefone_unique extends Migration
{
    public function safeUp()
    {
        // 1. Tornar nome opcional (convidado não tem nome)
        $this->alterColumn('{{%app_usuario}}', 'nome', $this->string(100)->null());

        // 2. Tornar email opcional
        $this->alterColumn('{{%app_usuario}}', 'email', $this->string(150)->null());

        // 3. Índice único para telefone
        try {
            $this->createIndex('idx-app_usuario-telefone-unique', '{{%app_usuario}}', 'telefone', true);
        } catch (\Exception $e) {
            // já existe
        }

        // 4. device_id
        $table = Yii::$app->db->schema->getTableSchema('{{%app_usuario}}');
        if (!isset($table->columns['device_id'])) {
            $this->addColumn('{{%app_usuario}}', 'device_id', $this->string(100)->null());
        }
        
        try {
            $this->createIndex('idx-app_usuario-device_id', '{{%app_usuario}}', 'device_id');
        } catch (\Exception $e) {
            // já existe
        }
    }

    public function safeDown()
    {
        // Remove device_id
        try {
            $this->dropIndex('idx-app_usuario-device_id', '{{%app_usuario}}');
        } catch (\Exception $e) {}
        
        $table = Yii::$app->db->schema->getTableSchema('{{%app_usuario}}');
        if (isset($table->columns['device_id'])) {
            $this->dropColumn('{{%app_usuario}}', 'device_id');
        }

        // Remove índice telefone
        try {
            $this->dropIndex('idx-app_usuario-telefone-unique', '{{%app_usuario}}');
        } catch (\Exception $e) {}

        // Reverte nome
        $this->alterColumn('{{%app_usuario}}', 'nome', $this->string(100)->notNull());

        // Reverte email
        $this->alterColumn('{{%app_usuario}}', 'email', $this->string(150)->notNull()->unique());
    }
}