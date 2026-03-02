<?php

use yii\db\Migration;

class m260302_135354_add_foreign_keys_to_produtos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        // FK para subcategorias
        $this->addForeignKey(
            'fk-produtos-subcategoria_id',
            '{{%produtos}}',
            'subcategoria_id',
            '{{%subcategorias}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-produtos-subcategoria_id', '{{%produtos}}');
        $this->dropForeignKey('fk-produtos-loja_id', '{{%produtos}}');
    }
}