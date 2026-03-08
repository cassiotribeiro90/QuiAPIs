<?php

use yii\db\Migration;

class m260308_130936_create_store_endereco_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%store_endereco}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTO ==========
            'usuario_id' => $this->integer()->notNull(),
            
            // ========== IDENTIFICAÇÃO ==========
            'tipo' => "ENUM('residencial', 'comercial', 'loja') NOT NULL DEFAULT 'comercial'",
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
            
            // ========== CONTATO ==========
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
            'fk-store_endereco-usuario_id',
            '{{%store_endereco}}',
            'usuario_id',
            '{{%store_usuario}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-store_endereco-usuario_id', '{{%store_endereco}}', 'usuario_id');
        $this->createIndex('idx-store_endereco-padrao', '{{%store_endereco}}', 'padrao');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-store_endereco-usuario_id', '{{%store_endereco}}');
        $this->dropTable('{{%store_endereco}}');
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