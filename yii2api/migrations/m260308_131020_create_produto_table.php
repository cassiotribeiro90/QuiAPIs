<?php

use yii\db\Migration;

class m260308_131020_create_produto_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%produto}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'loja_id' => $this->integer()->notNull(),
            'subcategoria_id' => $this->integer()->null(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(255)->notNull(),
            'descricao' => $this->text(),
            'slug' => $this->string(255)->notNull()->unique(),
            
            // ========== PREÇOS ==========
            'preco' => $this->decimal(10,2)->notNull(),
            'preco_promocional' => $this->decimal(10,2)->null(),
            
            // ========== MÍDIA ==========
            'imagem' => $this->string(500)->null(),
            'imagens' => $this->json()->null(),
            
            // ========== COMPOSIÇÃO ==========
            'ingredientes' => $this->json()->null(),
            'ingredientes_texto' => $this->text()->null(),
            'calorias' => $this->integer()->null(),
            'peso_gramas' => $this->integer()->null(),
            
            // ========== INFORMAÇÕES NUTRICIONAIS ==========
            'contem_gluten' => $this->boolean()->defaultValue(false),
            'contem_lactose' => $this->boolean()->defaultValue(false),
            'vegano' => $this->boolean()->defaultValue(false),
            'vegetariano' => $this->boolean()->defaultValue(false),
            'apimentado' => $this->boolean()->defaultValue(false),
            
            // ========== CAMPOS INOVADORES ==========
            'selos' => $this->json()->null(),
            'disponivel_inicio' => $this->time()->null(),
            'disponivel_fim' => $this->time()->null(),
            'disponivel_dias' => $this->json()->null(),
            'ultima_venda_em' => $this->timestamp()->null(),
            'vendas_hoje' => $this->integer()->defaultValue(0),
            
            // ========== VARIAÇÕES ==========
            'variacoes' => $this->json()->null(),
            'opcoes' => $this->json()->null(),
            'tempo_preparo_min' => $this->integer()->null(),
            
            // ========== DISPONIBILIDADE ==========
            'disponivel' => $this->boolean()->defaultValue(true),
            'estoque' => $this->integer()->defaultValue(0),
            
            // ========== CONTROLE ==========
            'ordem' => $this->integer()->defaultValue(0),
            
            // ========== AVALIAÇÕES ==========
            'nota_media' => $this->decimal(2,1)->defaultValue(0),
            'total_avaliacoes' => $this->integer()->defaultValue(0),
            
            // ========== MÉTRICAS ==========
            'visualizacoes' => $this->integer()->defaultValue(0),
            'cliques' => $this->integer()->defaultValue(0),
            
            // ========== STATUS ==========
            'ativo' => $this->boolean()->defaultValue(true),
            'destaque' => $this->boolean()->defaultValue(false),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deletado_em' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-produto-loja_id',
            '{{%produto}}',
            'loja_id',
            '{{%loja}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-produto-subcategoria_id',
            '{{%produto}}',
            'subcategoria_id',
            '{{%subcategoria}}',
            'id',
            'SET NULL'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-produto-slug', '{{%produto}}', 'slug');
        $this->createIndex('idx-produto-loja_id', '{{%produto}}', 'loja_id');
        $this->createIndex('idx-produto-subcategoria_id', '{{%produto}}', 'subcategoria_id');
        $this->createIndex('idx-produto-ativo', '{{%produto}}', 'ativo');
        $this->createIndex('idx-produto-destaque', '{{%produto}}', 'destaque');
        $this->createIndex('idx-produto-nota_media', '{{%produto}}', 'nota_media');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-produto-subcategoria_id', '{{%produto}}');
        $this->dropForeignKey('fk-produto-loja_id', '{{%produto}}');
        $this->dropTable('{{%produto}}');
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