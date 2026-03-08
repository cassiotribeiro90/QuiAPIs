<?php

use yii\db\Migration;

class m260308_130935_create_app_endereco_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%app_endereco}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTO ==========
            'usuario_id' => $this->integer()->notNull(),
            
            // ========== IDENTIFICAÇÃO ==========
            'tipo' => "ENUM('residencial', 'comercial', 'entrega', 'cobranca') NOT NULL DEFAULT 'entrega'",
            'apelido' => $this->string(50)->null(),
            
            // ========== ENDEREÇO ==========
            'cep' => $this->string(9)->notNull(),
            'logradouro' => $this->string(255)->notNull(),
            'numero' => $this->string(20)->notNull(),
            'complemento' => $this->string(255)->null(),
            'bairro' => $this->string(100)->notNull(),
            'cidade' => $this->string(100)->notNull(),
            'uf' => $this->string(2)->notNull(),
            
            // ========== COORDENADAS ==========
            'latitude' => $this->decimal(10,8)->null(),
            'longitude' => $this->decimal(11,8)->null(),
            
            // ========== REFERÊNCIA ==========
            'referencia' => $this->string(255)->null(),
            
            // ========== CONTATO ==========
            'destinatario' => $this->string(100)->null(),
            'telefone_contato' => $this->string(20)->null(),
            
            // ========== PREFERÊNCIAS ==========
            'padrao' => $this->boolean()->defaultValue(false),
            'ativo' => $this->boolean()->defaultValue(true),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deletado_em' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-app_endereco-usuario_id',
            '{{%app_endereco}}',
            'usuario_id',
            '{{%app_usuario}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-app_endereco-usuario_id', '{{%app_endereco}}', 'usuario_id');
        $this->createIndex('idx-app_endereco-padrao', '{{%app_endereco}}', 'padrao');
        $this->createIndex('idx-app_endereco-usuario_padrao', '{{%app_endereco}}', ['usuario_id', 'padrao']);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-app_endereco-usuario_id', '{{%app_endereco}}');
        $this->dropTable('{{%app_endereco}}');
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