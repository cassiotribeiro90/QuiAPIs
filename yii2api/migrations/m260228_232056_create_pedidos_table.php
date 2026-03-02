<?php

use yii\db\Migration;

/**
 * Migration para criação da tabela de pedidos
 * 
 * Relacionamentos:
 * - 1 pedido → 1 usuario
 * - 1 pedido → 1 loja
 * - 1 pedido → N itens (via JSON)
 * 
 * Status:
 * - novo, aguardando, confirmado, preparando, pronto, saiu, entregue, cancelado
 */
class m260228_232056_create_pedidos_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%pedidos}}', [
            'id' => $this->primaryKey(),
            
            // ========== IDENTIFICAÇÃO ==========
            'codigo' => $this->string(50)->unique(),
            
            // ========== RELACIONAMENTOS ==========
            'usuario_id' => $this->integer()->notNull(),
            'loja_id' => $this->integer()->notNull(),
            'endereco_id' => $this->integer()->null()->comment('endereço de entrega escolhido'),
            
            // ========== STATUS ==========
            //    'status' => "ENUM(
            //    'novo',               // 🆕 Pedido criado
            //    'aguardando',         // ⏳ Aguardando confirmação
            //    'confirmado',         // ✅ Loja confirmou
            //    'preparando',         // 👨‍🍳 Preparando
            //    'pronto',             // 🍱 Pronto para retirada
            //    'saiu',               // 🛵 Saiu para entrega
            //    'entregue',           // 📦 Entregue
            //    'cancelado'           // ❌ Cancelado
            // ) NOT NULL DEFAULT 'novo'",

            'status' => "ENUM(
                'novo', 
                'aguardando', 
                'confirmado', 
                'preparando', 
                'pronto', 
                'saiu', 
                'entregue', 
                'cancelado'
            ) NOT NULL DEFAULT 'novo'",
            
            'status_historico' => $this->json()->null()->comment('timeline do pedido'),
            
            // ========== DATAS ==========
            'data_pedido' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'data_confirmacao' => $this->timestamp()->null(),
            'data_preparo' => $this->timestamp()->null(),
            'data_saida' => $this->timestamp()->null(),
            'data_entrega' => $this->timestamp()->null(),
            'data_cancelamento' => $this->timestamp()->null(),
            
            // ========== ITENS ==========
            'itens' => $this->json()->notNull()->comment('array de itens com quantidade, preço, opções'),
            'itens_count' => $this->integer()->defaultValue(0),
            'observacoes' => $this->text()->null(),
            
            // ========== VALORES ==========
            'subtotal' => $this->decimal(10,2)->notNull(),
            'taxa_entrega' => $this->decimal(10,2)->defaultValue(0),
            'desconto' => $this->decimal(10,2)->defaultValue(0),
            'total' => $this->decimal(10,2)->notNull(),
            
            // ========== PAGAMENTO ==========
            'forma_pagamento' => "ENUM(
                'credito', 
                'debito', 
                'dinheiro', 
                'pix', 
                'vr'
            ) NOT NULL",
            
            'pagamento_status' => "ENUM(
                'pendente',
                'aprovado',
                'recusado',
                'cancelado'
            ) NOT NULL DEFAULT 'pendente'",
            
            'troco_para' => $this->decimal(10,2)->null()->comment('se pagamento em dinheiro'),
            'pagamento_detalhes' => $this->json()->null(),
            
            // ========== ENTREGA ==========
            'endereco_entrega' => $this->json()->null()->comment('snapshot do endereço usado'),
            'tempo_espera_min' => $this->integer()->comment('tempo estimado no momento do pedido'),
            'distancia_km' => $this->decimal(5,2)->null(),
            
            // ========== AVALIAÇÃO ==========
            'avaliacao_nota' => $this->integer()->null()->comment('1-5'),
            'avaliacao_comentario' => $this->text()->null(),
            'avaliacao_resposta' => $this->text()->null()->comment('resposta da loja'),
            'avaliacao_at' => $this->timestamp()->null(),
            
            // ========== 🚀 CAMPOS INOVADORES ==========
            
            // 🔥 Tempo real
            'tempo_real_min' => $this->integer()->null()->comment('tempo real até entrega'),
            
            // 📍 Localização em tempo real (entregador)
            'entregador_lat' => $this->decimal(10,8)->null(),
            'entregador_lng' => $this->decimal(11,8)->null(),
            'entregador_updated_at' => $this->timestamp()->null(),
            
            // ========== MÉTRICAS ==========
            'cancelado_por' => "ENUM('cliente', 'loja', 'sistema') NULL",
            'cancelado_motivo' => $this->text()->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ]);





        // ========== ÍNDICES ==========
        $this->createIndex('idx-pedidos-codigo', '{{%pedidos}}', 'codigo');
        $this->createIndex('idx-pedidos-usuario_id', '{{%pedidos}}', 'usuario_id');
        $this->createIndex('idx-pedidos-loja_id', '{{%pedidos}}', 'loja_id');
        $this->createIndex('idx-pedidos-status', '{{%pedidos}}', 'status');
        $this->createIndex('idx-pedidos-data_pedido', '{{%pedidos}}', 'data_pedido');
        $this->createIndex('idx-pedidos-forma_pagamento', '{{%pedidos}}', 'forma_pagamento');
        $this->createIndex('idx-pedidos-pagamento_status', '{{%pedidos}}', 'pagamento_status');
        $this->createIndex('idx-pedidos-avaliacao_nota', '{{%pedidos}}', 'avaliacao_nota');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-pedidos-endereco_id', '{{%pedidos}}');
        $this->dropForeignKey('fk-pedidos-loja_id', '{{%pedidos}}');
        $this->dropForeignKey('fk-pedidos-usuario_id', '{{%pedidos}}');
        $this->dropTable('{{%pedidos}}');
    }
}