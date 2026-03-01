<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%avaliacoes}}`.
 */
class m260228_232116_create_avaliacoes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%avaliacoes}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%avaliacoes}}');
    }
}
