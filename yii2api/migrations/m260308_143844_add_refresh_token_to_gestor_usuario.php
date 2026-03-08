<?php
// migrations/m260310_000016_add_refresh_token_to_gestor_usuario.php

use yii\db\Migration;

class m260308_143844_add_refresh_token_to_gestor_usuario extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%gestor_usuario}}', 'refresh_token', $this->string(255)->null()->unique());
        $this->addColumn('{{%gestor_usuario}}', 'refresh_token_expira_em', $this->timestamp()->null());
        
        $this->createIndex('idx-gestor_usuario-refresh_token', '{{%gestor_usuario}}', 'refresh_token');
    }
    
    public function safeDown()
    {
        $this->dropIndex('idx-gestor_usuario-refresh_token', '{{%gestor_usuario}}');
        $this->dropColumn('{{%gestor_usuario}}', 'refresh_token');
        $this->dropColumn('{{%gestor_usuario}}', 'refresh_token_expira_em');
    }
}