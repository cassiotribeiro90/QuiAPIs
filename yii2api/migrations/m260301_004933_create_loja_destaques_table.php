<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%loja_destaques}}`.
 */
class m260301_004933_create_loja_destaques_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%loja_destaques}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%loja_destaques}}');
    }
}
