<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pedidos}}`.
 */
class m260228_232056_create_pedidos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pedidos}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pedidos}}');
    }
}
