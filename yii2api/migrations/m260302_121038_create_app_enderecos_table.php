<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%app_enderecos}}`.
 */
class m260302_121038_create_app_enderecos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%app_enderecos}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%app_enderecos}}');
    }
}
