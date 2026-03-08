<?php

use yii\db\Migration;

class m260308_131000_create_loja_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%loja}}', [
            'id' => $this->primaryKey(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(255)->notNull(),
            'descricao' => $this->text(),
            'slug' => $this->string(255)->notNull()->unique(),
            
            // ========== CATEGORIA ==========
            'categoria' => $this->string(100)->notNull(),
            
            // ========== MÍDIA ==========
            'logo' => $this->string(500)->null(),
            'capa' => $this->string(500)->null(),
            
            // ========== AVALIAÇÃO ==========
            'nota_media' => $this->decimal(2,1)->defaultValue(0),
            'total_avaliacoes' => $this->integer()->defaultValue(0),
            
            // ========== ENTREGA ==========
            'tempo_entrega_min' => $this->integer()->notNull(),
            'tempo_entrega_max' => $this->integer()->notNull(),
            'taxa_entrega' => $this->decimal(10,2)->defaultValue(0),
            'pedido_minimo' => $this->decimal(10,2)->defaultValue(0),
            
            // ========== ENDEREÇO ==========
            'cep' => $this->string(9)->notNull(),
            'logradouro' => $this->string(255)->notNull(),
            'numero' => $this->string(20)->notNull(),
            'complemento' => $this->string(255)->null(),
            'bairro' => $this->string(100)->notNull(),
            'cidade' => $this->string(100)->notNull(),
            'uf' => $this->string(2)->notNull(),
            'latitude' => $this->decimal(10,8)->null(),
            'longitude' => $this->decimal(11,8)->null(),
            
            // ========== CONTATO ==========
            'telefone' => $this->string(20)->notNull(),
            'whatsapp' => $this->string(20)->null(),
            'email' => $this->string(255)->null(),
            'instagram' => $this->string(255)->null(),
            
            // ========== STATUS ==========
            'status' => "ENUM('ativo', 'inativo', 'fechado', 'revisao') NOT NULL DEFAULT 'revisao'",
            'verificado' => $this->boolean()->defaultValue(false),
            'destaque' => $this->boolean()->defaultValue(false),
            
            // ========== CAMPOS INOVADORES ==========
            'trending_score' => $this->integer()->defaultValue(0),
            'fluxo_status' => "ENUM('vazio', 'normal', 'cheio', 'lotado') NOT NULL DEFAULT 'normal'",
            'cor_tema' => $this->string(7)->defaultValue('#FF6B6B'),
            
            // ========== CONFIGURAÇÕES ==========
            'configuracoes' => $this->json()->null(),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deletado_em' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== ÍNDICES ==========
        $this->createIndex('idx-loja-slug', '{{%loja}}', 'slug');
        $this->createIndex('idx-loja-categoria', '{{%loja}}', 'categoria');
        $this->createIndex('idx-loja-status', '{{%loja}}', 'status');
        $this->createIndex('idx-loja-nota_media', '{{%loja}}', 'nota_media');
        $this->createIndex('idx-loja-trending_score', '{{%loja}}', 'trending_score');
        $this->createIndex('idx-loja-cidade', '{{%loja}}', 'cidade');
    }

    public function safeDown()
    {
        $this->dropTable('{{%loja}}');
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