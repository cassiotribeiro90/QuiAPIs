<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lojas}}`.
 */
class m260228_232036_create_lojas_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lojas}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lojas}}');
    }
}
