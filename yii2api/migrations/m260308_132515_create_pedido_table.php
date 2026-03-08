<?php

use yii\db\Migration;

class m260308_132515_create_pedido_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%pedido}}', [
            'id' => $this->primaryKey(),
            
            // ========== IDENTIFICAÇÃO ==========
            'codigo' => $this->string(50)->notNull()->unique(),
            
            // ========== RELACIONAMENTOS ==========
            'usuario_id' => $this->integer()->notNull(),
            'loja_id' => $this->integer()->notNull(),
            'endereco_id' => $this->integer()->null(),
            
            // ========== STATUS ==========
            'status' => "ENUM('novo', 'aguardando', 'confirmado', 'preparando', 'pronto', 'saiu', 'entregue', 'cancelado') NOT NULL DEFAULT 'novo'",
            'status_historico' => $this->json()->null(),
            
            // ========== DATAS ==========
            'data_pedido' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'data_confirmacao' => $this->timestamp()->null(),
            'data_preparo' => $this->timestamp()->null(),
            'data_saida' => $this->timestamp()->null(),
            'data_entrega' => $this->timestamp()->null(),
            'data_cancelamento' => $this->timestamp()->null(),
            
            // ========== VALORES ==========
            'subtotal' => $this->decimal(10,2)->notNull(),
            'taxa_entrega' => $this->decimal(10,2)->defaultValue(0),
            'desconto' => $this->decimal(10,2)->defaultValue(0),
            'total' => $this->decimal(10,2)->notNull(),
            
            // ========== PAGAMENTO ==========
            'forma_pagamento' => "ENUM('credito', 'debito', 'dinheiro', 'pix', 'vr') NOT NULL",
            'pagamento_status' => "ENUM('pendente', 'aprovado', 'recusado', 'cancelado') NOT NULL DEFAULT 'pendente'",
            'troco_para' => $this->decimal(10,2)->null(),
            'pagamento_detalhes' => $this->json()->null(),
            
            // ========== ENTREGA ==========
            'endereco_entrega' => $this->json()->null(),
            'tempo_espera_min' => $this->integer()->null(),
            'distancia_km' => $this->decimal(5,2)->null(),
            
            // ========== OBSERVAÇÕES ==========
            'observacoes' => $this->text()->null(),
            
            // ========== CAMPOS INOVADORES ==========
            'tempo_real_min' => $this->integer()->null(),
            'entregador_lat' => $this->decimal(10,8)->null(),
            'entregador_lng' => $this->decimal(11,8)->null(),
            'entregador_atualizado_em' => $this->timestamp()->null(),
            
            // ========== CANCELAMENTO ==========
            'cancelado_por' => "ENUM('cliente', 'loja', 'sistema') NULL",
            'cancelado_motivo' => $this->text()->null(),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deletado_em' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-pedido-usuario_id',
            '{{%pedido}}',
            'usuario_id',
            '{{%app_usuario}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-pedido-loja_id',
            '{{%pedido}}',
            'loja_id',
            '{{%loja}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-pedido-endereco_id',
            '{{%pedido}}',
            'endereco_id',
            '{{%app_endereco}}',
            'id',
            'SET NULL'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-pedido-codigo', '{{%pedido}}', 'codigo');
        $this->createIndex('idx-pedido-usuario_id', '{{%pedido}}', 'usuario_id');
        $this->createIndex('idx-pedido-loja_id', '{{%pedido}}', 'loja_id');
        $this->createIndex('idx-pedido-status', '{{%pedido}}', 'status');
        $this->createIndex('idx-pedido-data_pedido', '{{%pedido}}', 'data_pedido');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-pedido-endereco_id', '{{%pedido}}');
        $this->dropForeignKey('fk-pedido-loja_id', '{{%pedido}}');
        $this->dropForeignKey('fk-pedido-usuario_id', '{{%pedido}}');
        $this->dropTable('{{%pedido}}');
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