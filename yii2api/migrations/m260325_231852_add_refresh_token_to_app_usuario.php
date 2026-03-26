<?php

use yii\db\Migration;

class m260325_231852_add_refresh_token_to_app_usuario extends Migration
{
    public function safeUp()
    {
        // Adiciona coluna refresh_token
        $this->addColumn('{{%app_usuario}}', 'refresh_token', $this->string(255)->null()->unique());
        $this->addColumn('{{%app_usuario}}', 'refresh_token_expira_em', $this->timestamp()->null());
        
        // Cria índice para consultas rápidas
        $this->createIndex('idx-app_usuario-refresh_token', '{{%app_usuario}}', 'refresh_token');
    }
    
    public function safeDown()
    {
        $this->dropIndex('idx-app_usuario-refresh_token', '{{%app_usuario}}');
        $this->dropColumn('{{%app_usuario}}', 'refresh_token_expira_em');
        $this->dropColumn('{{%app_usuario}}', 'refresh_token');
    }
}
