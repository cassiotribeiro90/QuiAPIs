<?php

use yii\db\Migration;

/**
 * Class m260413_XXXXXX_add_social_login_apple
 * Adiciona suporte a login com Apple ID e otimiza campos sociais
 */
class m260413_151923_add_social_login extends Migration
{
    public function safeUp()
    {
        // 1. Adicionar coluna apple_id (se não existir)
        if (!$this->db->getTableSchema('{{%app_usuario}}')->getColumn('apple_id')) {
            $this->addColumn('{{%app_usuario}}', 'apple_id', $this->string(255)->null()->unique()->after('facebook_id'));
            $this->createIndex('idx-app_usuario-apple_id', '{{%app_usuario}}', 'apple_id', true);
        }

        // 2. Adicionar coluna avatar (se não existir) - fallback
        if (!$this->db->getTableSchema('{{%app_usuario}}')->getColumn('avatar')) {
            $this->addColumn('{{%app_usuario}}', 'avatar', $this->string(500)->null()->after('apple_id'));
        }

        // 3. Garantir que google_id e facebook_id tenham índices únicos
        $tableSchema = $this->db->getTableSchema('{{%app_usuario}}');
        
        if ($tableSchema->getColumn('google_id') && !$this->indexExists('{{%app_usuario}}', 'google_id')) {
            $this->createIndex('idx-app_usuario-google_id', '{{%app_usuario}}', 'google_id', true);
        }
        
        if ($tableSchema->getColumn('facebook_id') && !$this->indexExists('{{%app_usuario}}', 'facebook_id')) {
            $this->createIndex('idx-app_usuario-facebook_id', '{{%app_usuario}}', 'facebook_id', true);
        }

        // 4. Adicionar campo provider (opcional) para rastrear provedor do último login
        if (!$tableSchema->getColumn('ultimo_login_provider')) {
            $this->addColumn(
                '{{%app_usuario}}', 
                'ultimo_login_provider', 
                "ENUM('email', 'google', 'facebook', 'apple') DEFAULT NULL AFTER `ultimo_login_ip`"
            );
        }
    }

    public function safeDown()
    {
        $tableSchema = $this->db->getTableSchema('{{%app_usuario}}');
        
        if ($tableSchema->getColumn('apple_id')) {
            $this->dropIndex('idx-app_usuario-apple_id', '{{%app_usuario}}');
            $this->dropColumn('{{%app_usuario}}', 'apple_id');
        }
        
        if ($tableSchema->getColumn('ultimo_login_provider')) {
            $this->dropColumn('{{%app_usuario}}', 'ultimo_login_provider');
        }
        
        // Não removemos avatar pois pode ser usado por outros recursos
    }

    private function indexExists($table, $indexName)
    {
        $indexes = $this->db->getSchema()->getTableIndexes($table);
        return isset($indexes[$indexName]);
    }
}