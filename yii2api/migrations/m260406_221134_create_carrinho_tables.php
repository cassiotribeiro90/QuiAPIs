<?php
// migrations/m260406_000000_create_carrinho_tables.php

use yii\db\Migration;

class m260406_221134_create_carrinho_tables extends Migration
{
    public function safeUp()
    {
        // ===== TABELA CARRINHO =====
        $this->createTable('{{%carrinho}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'usuario_id' => $this->integer()->notNull(),
            'loja_id' => $this->integer()->notNull(),
            
            // ========== STATUS ==========
            'status' => "ENUM('ativo', 'finalizado', 'abandonado') NOT NULL DEFAULT 'ativo'",
            
            // ========== RESUMO (cache) ==========
            'total_itens' => $this->integer()->defaultValue(0),
            'subtotal' => $this->decimal(10,2)->defaultValue(0),
            
            // ========== METADADOS ==========
            'metadata' => $this->json()->null(),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        // ===== CHAVES ESTRANGEIRAS =====
        $this->addForeignKey(
            'fk-carrinho-usuario_id',
            '{{%carrinho}}',
            'usuario_id',
            '{{%app_usuario}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-carrinho-loja_id',
            '{{%carrinho}}',
            'loja_id',
            '{{%loja}}',
            'id',
            'CASCADE'
        );

        // ===== ÍNDICES =====
        $this->createIndex('idx-carrinho-usuario_id', '{{%carrinho}}', 'usuario_id');
        $this->createIndex('idx-carrinho-status', '{{%carrinho}}', 'status');
        $this->createIndex('idx-carrinho-usuario_status', '{{%carrinho}}', ['usuario_id', 'status']);

        // ===== TABELA CARRINHO_ITEM =====
        $this->createTable('{{%carrinho_item}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTOS ==========
            'carrinho_id' => $this->integer()->notNull(),
            'produto_id' => $this->integer()->notNull(),
            
            // ========== QUANTIDADE ==========
            'quantidade' => $this->integer()->notNull()->defaultValue(1),
            
            // ========== PREÇOS (SNAPSHOT) ==========
            'preco_unitario' => $this->decimal(10,2)->notNull(),
            'preco_adicionais' => $this->decimal(10,2)->defaultValue(0),
            'preco_total' => $this->decimal(10,2)->notNull(),
            
            // ========== SNAPSHOT DO PRODUTO ==========
            'produto_nome' => $this->string(255)->notNull(),
            'produto_descricao' => $this->text()->null(),
            'produto_imagem' => $this->string(500)->null(),
            
            // ========== OPÇÕES ESCOLHIDAS ==========
            'opcoes' => $this->json()->null(),        // IDs das opções selecionadas
            'opcoes_detalhes' => $this->json()->null(), // Detalhes completos (nome, preço)
            
            // ========== OBSERVAÇÕES ==========
            'observacao' => $this->text()->null(),
            
            // ========== METADADOS ==========
            'metadata' => $this->json()->null(),      // Para meio a meio, combos, etc
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        // ===== CHAVES ESTRANGEIRAS =====
        $this->addForeignKey(
            'fk-carrinho_item-carrinho_id',
            '{{%carrinho_item}}',
            'carrinho_id',
            '{{%carrinho}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-carrinho_item-produto_id',
            '{{%carrinho_item}}',
            'produto_id',
            '{{%produto}}',
            'id',
            'CASCADE'
        );

        // ===== ÍNDICES =====
        $this->createIndex('idx-carrinho_item-carrinho_id', '{{%carrinho_item}}', 'carrinho_id');
        $this->createIndex('idx-carrinho_item-produto_id', '{{%carrinho_item}}', 'produto_id');
        
        echo "✅ Tabelas 'carrinho' e 'carrinho_item' criadas com sucesso!\n";
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-carrinho_item-produto_id', '{{%carrinho_item}}');
        $this->dropForeignKey('fk-carrinho_item-carrinho_id', '{{%carrinho_item}}');
        $this->dropForeignKey('fk-carrinho-loja_id', '{{%carrinho}}');
        $this->dropForeignKey('fk-carrinho-usuario_id', '{{%carrinho}}');
        
        $this->dropTable('{{%carrinho_item}}');
        $this->dropTable('{{%carrinho}}');
        
        echo "✅ Tabelas removidas com sucesso!\n";
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