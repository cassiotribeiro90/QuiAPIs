<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lojas}}`.
 */
class m260228_232036_create_lojas_table extends Migration
{
        public function safeUp()
    {
        $this->createTable('{{%lojas}}', [
            // ========== IDENTIFICAÇÃO ==========
            'id' => $this->primaryKey(),
            'nome' => $this->string(255)->notNull(),
            'descricao' => $this->text(),
            'slug' => $this->string(255)->unique(),
            
            // ========== CATEGORIA ==========
            'categoria' => $this->string(100)->notNull(),
            
            // ========== MÍDIA ==========
            'logo' => $this->string(500)->null(),
            'capa' => $this->string(500)->null(),
            
            // ========== AVALIAÇÃO ==========
            'nota_media' => $this->decimal(2,1)->defaultValue(0),
            'total_avaliacoes' => $this->integer()->defaultValue(0),
            
            // ========== ENTREGA ==========
            'tempo_entrega_min' => $this->integer()->notNull()->comment('minutos'),
            'tempo_entrega_max' => $this->integer()->notNull()->comment('minutos'),
            'taxa_entrega' => $this->decimal(10,2)->defaultValue(0),
            'pedido_minimo' => $this->decimal(10,2)->defaultValue(0),
            
            // ========== ENDEREÇO ==========
            'endereco_rua' => $this->string(255)->notNull(),
            'endereco_numero' => $this->string(20),
            'endereco_complemento' => $this->string(255),
            'endereco_bairro' => $this->string(100)->notNull(),
            'endereco_cidade' => $this->string(100)->notNull(),
            'endereco_uf' => $this->string(2)->notNull(),
            'endereco_cep' => $this->string(9)->notNull(),
            'endereco_lat' => $this->decimal(10,8),
            'endereco_lng' => $this->decimal(11,8),
            
            // ========== CONTATO ==========
            'telefone' => $this->string(20)->notNull(),
            'whatsapp' => $this->string(20),
            'email' => $this->string(255),
            'instagram' => $this->string(255),
            
            // ========== STATUS ==========
            'ativo' => $this->boolean()->defaultValue(true),
            'destaque' => $this->boolean()->defaultValue(false),
            'verificado' => $this->boolean()->defaultValue(false),
            
            // ========== 🚀 CAMPOS INOVADORES ==========
            
            // 🔥 Bombando (score de popularidade)
            'trending_score' => $this->integer()->defaultValue(0)->comment('Score 0-100 calculado por vendas recentes'),
            
            // 🟢 Status do fluxo de pedidos
            'fluxo_status' => "ENUM('vazio', 'normal', 'cheio', 'super_lotado') NOT NULL DEFAULT 'normal'",
            
            // 🎨 Cor tema da loja
            'cor_tema' => $this->string(7)->defaultValue('#FF6B6B')->comment('Cor principal (hex)'),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ]);

        // ========== ÍNDICES ==========
        $this->createIndex('idx-lojas-categoria', '{{%lojas}}', 'categoria');
        $this->createIndex('idx-lojas-nota_media', '{{%lojas}}', 'nota_media');
        $this->createIndex('idx-lojas-trending_score', '{{%lojas}}', 'trending_score');
        $this->createIndex('idx-lojas-fluxo_status', '{{%lojas}}', 'fluxo_status');
        $this->createIndex('idx-lojas-ativo', '{{%lojas}}', 'ativo');
        $this->createIndex('idx-lojas-destaque', '{{%lojas}}', 'destaque');
    }

    public function safeDown()
    {
        $this->dropTable('{{%lojas}}');
    }
}
