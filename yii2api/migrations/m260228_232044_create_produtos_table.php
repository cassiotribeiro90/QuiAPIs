<?php

use yii\db\Migration;

/**
 * Migration para criação da tabela de produtos
 * 
 * Relacionamentos:
 * - 1 produto → 1 loja (obrigatório)
 * - 1 produto → 1 subcategoria (opcional)
 * 
 * Badge tipos (selos):
 *   - fogo     (🔥 Bombando)
 *   - estrela  (⭐ Destaque)
 *   - folha    (🌱 Vegano)
 *   - coracao  (❤️ Recomendado)
 *   - novo     (🆕 Novidade)
 *   - premio   (�🏻 Prêmio)
 *   - porcentagem (% Off)
 */
class m260228_232044_create_produtos_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%produtos}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'loja_id' => $this->integer()->notNull(),
            'subcategoria_id' => $this->integer()->null(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(255)->notNull(),
            'descricao' => $this->text(),
            'slug' => $this->string(255)->unique(),
            
            // ========== PREÇOS ==========
            'preco' => $this->decimal(10,2)->notNull(),
            'preco_promocional' => $this->decimal(10,2)->null(),
            
            // ========== MÍDIA ==========
            'imagem' => $this->string(500)->null(),
            'imagens' => $this->json()->null(),
            
            // ========== COMPOSIÇÃO ==========
            'ingredientes' => $this->json()->null()->comment('Lista de ingredientes'),
            'ingredientes_texto' => $this->text()->null(),
            'calorias' => $this->integer()->null(),
            'peso_gramas' => $this->integer()->null(),
            
            // ========== INFORMAÇÕES NUTRICIONAIS ==========
            'contem_gluten' => $this->boolean()->defaultValue(false),
            'contem_lactose' => $this->boolean()->defaultValue(false),
            'vegano' => $this->boolean()->defaultValue(false),
            'vegetariano' => $this->boolean()->defaultValue(false),
            'apimentado' => $this->boolean()->defaultValue(false),
            
            // ========== 🚀 CAMPOS INOVADORES ==========
            
            // 🔥 Selos especiais (códigos)
            'selos' => $this->json()->null()->comment('["bombando", "novidade", "chef", "premio"]'),
            
            // ⏰ Disponibilidade por período
            'disponivel_inicio' => $this->time()->null(),
            'disponivel_fim' => $this->time()->null(),
            'disponivel_dias' => $this->json()->null()->comment('[1,2,3,4,5,6,7]'),
            
            // 📈 Métricas em tempo real
            'ultima_venda_at' => $this->timestamp()->null(),
            'vendas_hoje' => $this->integer()->defaultValue(0),
            
            // ========== VARIAÇÕES E OPÇÕES ==========
            'variacoes' => $this->json()->null()->comment('{"tamanhos": ["P", "M", "G"], "precos": [10,15,20]}'),
            'opcoes' => $this->json()->null()->comment('{"sabores": ["choc", "mor"], "adicionais": ["bacon"]}'),
            
            // ========== PREPARO ==========
            'tempo_preparo_min' => $this->integer(),
            
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
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ]);

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-produtos-loja_id',
            '{{%produtos}}',
            'loja_id',
            '{{%lojas}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-produtos-subcategoria_id',
            '{{%produtos}}',
            'subcategoria_id',
            '{{%subcategorias}}',
            'id',
            'SET NULL'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-produtos-loja_id', '{{%produtos}}', 'loja_id');
        $this->createIndex('idx-produtos-subcategoria_id', '{{%produtos}}', 'subcategoria_id');
        $this->createIndex('idx-produtos-nota_media', '{{%produtos}}', 'nota_media');
        $this->createIndex('idx-produtos-ativo', '{{%produtos}}', 'ativo');
        $this->createIndex('idx-produtos-destaque', '{{%produtos}}', 'destaque');
        $this->createIndex('idx-produtos-vegano', '{{%produtos}}', 'vegano');
        $this->createIndex('idx-produtos-disponivel', '{{%produtos}}', 'disponivel');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-produtos-subcategoria_id', '{{%produtos}}');
        $this->dropForeignKey('fk-produtos-loja_id', '{{%produtos}}');
        $this->dropTable('{{%produtos}}');
    }
}