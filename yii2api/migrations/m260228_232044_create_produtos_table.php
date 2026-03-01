<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%produtos}}`.
 */
class m260228_232044_create_produtos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%produtos}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%produtos}}');
    }
}
