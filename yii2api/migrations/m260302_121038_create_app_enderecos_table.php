<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%app_enderecos}}`.
 * Relacionamento: 1 usuário (app) → N endereços
 */
class m260302_121038_create_app_enderecos_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%app_enderecos}}', [
            // ========== CHAVE PRIMÁRIA ==========
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTO ==========
            'usuario_id' => $this->integer()->notNull(),
            
            // ========== TIPO DE ENDEREÇO ==========
            'tipo' => "ENUM('residencial', 'comercial', 'entrega', 'cobranca') NOT NULL DEFAULT 'entrega'",
            'apelido' => $this->string(50)->null()->comment('ex: Casa, Trabalho, Mãe'),
            
            // ========== ENDEREÇO COMPLETO ==========
            'cep' => $this->string(9)->notNull(),
            'logradouro' => $this->string(255)->notNull(),
            'numero' => $this->string(20)->notNull(),
            'complemento' => $this->string(255)->null(),
            'bairro' => $this->string(100)->notNull(),
            'cidade' => $this->string(100)->notNull(),
            'uf' => $this->string(2)->notNull(),
            'pais' => $this->string(50)->defaultValue('Brasil'),
            
            // ========== COORDENADAS ==========
            'latitude' => $this->decimal(10, 8)->null(),
            'longitude' => $this->decimal(11, 8)->null(),
            
            // ========== PONTO DE REFERÊNCIA ==========
            'referencia' => $this->string(255)->null()->comment('ex: próximo ao mercado, em frente à praça'),
            
            // ========== CONTATO NO LOCAL ==========
            'destinatario' => $this->string(100)->null()->comment('nome da pessoa que recebe'),
            'telefone_contato' => $this->string(20)->null(),
            
            // ========== PREFERÊNCIAS ==========
            'padrao' => $this->boolean()->defaultValue(false)->comment('endereço padrão para entregas'),
            'ativo' => $this->boolean()->defaultValue(true),
            
            // ========== METADADOS ==========
            'metadata' => $this->json()->null()->comment('informações adicionais'),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-app_enderecos-usuario_id',
            '{{%app_enderecos}}',
            'usuario_id',
            '{{%app_usuarios}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-app_enderecos-usuario_id', '{{%app_enderecos}}', 'usuario_id');
        $this->createIndex('idx-app_enderecos-tipo', '{{%app_enderecos}}', 'tipo');
        $this->createIndex('idx-app_enderecos-padrao', '{{%app_enderecos}}', 'padrao');
        $this->createIndex('idx-app_enderecos-cep', '{{%app_enderecos}}', 'cep');
        $this->createIndex('idx-app_enderecos-cidade', '{{%app_enderecos}}', 'cidade');
        
        // ========== ÍNDICE COMPOSTO (usuario_id + padrao) ==========
        $this->createIndex('idx-app_enderecos-usuario_padrao', '{{%app_enderecos}}', ['usuario_id', 'padrao']);
        
        // ========== COMENTÁRIO ==========
        $this->addCommentOnTable('{{%app_enderecos}}', 'Endereços dos usuários do app');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-app_enderecos-usuario_id', '{{%app_enderecos}}');
        $this->dropTable('{{%app_enderecos}}');
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