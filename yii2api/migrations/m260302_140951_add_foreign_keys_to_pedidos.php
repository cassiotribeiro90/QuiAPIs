<?php

use yii\db\Migration;

class m260302_140951_add_foreign_keys_to_pedidos extends Migration
{
    public function safeUp()
    {
        // FK para endereços (agora a tabela já existe)
        $this->addForeignKey(
            'fk-pedidos-endereco_id',
            '{{%pedidos}}',
            'endereco_id',
            '{{%app_enderecos}}',  // Confirme o nome correto da tabela!
            'id',
            'SET NULL'
        );
    

            // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-pedidos-usuario_id',
            '{{%pedidos}}',
            'usuario_id',
            '{{%app_usuarios}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-pedidos-loja_id',
            '{{%pedidos}}',
            'loja_id',
            '{{%lojas}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-pedidos-endereco_id', '{{%pedidos}}');
    }
}