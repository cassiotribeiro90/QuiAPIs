<?php

use yii\db\Migration;

/**
 * Migration para criação da tabela pivot pedido_produtos
 * 
 * Relacionamentos:
 * - 1 pedido → N produtos
 * - 1 produto → N pedidos (via esta tabela)
 * 
 * Armazena o snapshot do produto no momento da compra
 * DESCONTO removido - fica apenas na tabela produtos
 */
class m260301_011930_create_pedidos_produtos_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%pedido_produtos}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'pedido_id' => $this->integer()->notNull(),
            'produto_id' => $this->integer()->notNull(),
            
            // ========== QUANTIDADE ==========
            'quantidade' => $this->integer()->notNull()->defaultValue(1),
            
            // ========== PREÇOS (SNAPSHOT) ==========
            'preco_unitario' => $this->decimal(10,2)->notNull()->comment('preço no momento da compra (já com desconto se houver)'),
            'preco_total' => $this->decimal(10,2)->notNull()->comment('quantidade * preco_unitario'),
            
            // ========== SNAPSHOT DO PRODUTO (para histórico) ==========
            'produto_nome' => $this->string(255)->notNull()->comment('nome do produto no momento'),
            'produto_descricao' => $this->text()->null(),
            'produto_imagem' => $this->string(500)->null(),
            
            // ========== OPÇÕES ESCOLHIDAS ==========
            'opcoes' => $this->json()->null()->comment('{"tamanho": "G", "sabor": "chocolate", "adicionais": ["bacon"]}'),
            'observacao' => $this->text()->null()->comment('observação por item (ex: "sem cebola")'),
            
            // ========== AVALIAÇÃO DO ITEM (opcional) ==========
            'avaliacao_nota' => $this->integer()->null()->comment('1-5'),
            'avaliacao_comentario' => $this->text()->null(),
            
            // ========== METADADOS ==========
            'metadata' => $this->json()->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-pedido_produtos-pedido_id',
            '{{%pedido_produtos}}',
            'pedido_id',
            '{{%pedidos}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-pedido_produtos-produto_id',
            '{{%pedido_produtos}}',
            'produto_id',
            '{{%produtos}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-pedido_produtos-pedido_id', '{{%pedido_produtos}}', 'pedido_id');
        $this->createIndex('idx-pedido_produtos-produto_id', '{{%pedido_produtos}}', 'produto_id');
        $this->createIndex('idx-pedido_produtos-avaliacao_nota', '{{%pedido_produtos}}', 'avaliacao_nota');
        
        // Índice composto para consultas de produtos mais vendidos
        $this->createIndex('idx-pedido_produtos-produto_quantidade', '{{%pedido_produtos}}', ['produto_id', 'quantidade']);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-pedido_produtos-produto_id', '{{%pedido_produtos}}');
        $this->dropForeignKey('fk-pedido_produtos-pedido_id', '{{%pedido_produtos}}');
        $this->dropTable('{{%pedido_produtos}}');
    }
}