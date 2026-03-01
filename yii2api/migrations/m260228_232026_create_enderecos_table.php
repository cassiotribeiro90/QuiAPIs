<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%enderecos}}`.
 */
class m260228_232026_create_enderecos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%enderecos}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%enderecos}}');
    }
}
