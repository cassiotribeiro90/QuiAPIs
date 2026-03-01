<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subcategorias}}`.
 */
class m260301_010137_create_subcategorias_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subcategorias}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subcategorias}}');
    }
}
