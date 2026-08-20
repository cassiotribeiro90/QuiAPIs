<?php

use yii\db\Migration;

class m260819_001535_expand_pedido_enums extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // ✅ Formas de pagamento expandidas
        $this->execute("ALTER TABLE pedido MODIFY COLUMN forma_pagamento ENUM(
            'credito',
            'debito',
            'dinheiro',
            'pix',
            'vr',
            'cartao_entrega',
            'cartao_credito',
            'cartao_debito',
            'vale_refeicao',
            'vale_alimentacao',
            'transferencia',
            'boleto',
            'outro'
        ) NOT NULL");

        // ✅ Status do pedido expandidos
        $this->execute("ALTER TABLE pedido MODIFY COLUMN status ENUM(
            'novo',
            'aguardando',
            'confirmado',
            'preparando',
            'pronto',
            'saiu',
            'entregue',
            'cancelado',
            'recusado'
        ) NOT NULL DEFAULT 'novo'");

        // ✅ Status de pagamento expandidos
        $this->execute("ALTER TABLE pedido MODIFY COLUMN pagamento_status ENUM(
            'pendente',
            'aprovado',
            'recusado',
            'cancelado',
            'estornado',
            'em_analise'
        ) NOT NULL DEFAULT 'pendente'");

        // ✅ NOVA COLUNA: pagamento_config (JSON)
        $this->addColumn('pedido', 'pagamento_config', $this->json()->null()->after('pagamento_detalhes'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove a coluna pagamento_config
        $this->dropColumn('pedido', 'pagamento_config');

        // Reverte formas de pagamento
        $this->execute("ALTER TABLE pedido MODIFY COLUMN forma_pagamento ENUM('credito','debito','dinheiro','pix','vr') NOT NULL");

        // Reverte status
        $this->execute("ALTER TABLE pedido MODIFY COLUMN status ENUM('novo','aguardando','confirmado','preparando','pronto','saiu','entregue','cancelado') NOT NULL DEFAULT 'novo'");

        // Reverte pagamento_status
        $this->execute("ALTER TABLE pedido MODIFY COLUMN pagamento_status ENUM('pendente','aprovado','recusado','cancelado') NOT NULL DEFAULT 'pendente'");
    }
}