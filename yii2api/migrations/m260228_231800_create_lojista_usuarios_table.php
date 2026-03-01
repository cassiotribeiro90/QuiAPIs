<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lojista_usuarios}}`.
 */
class m260228_231800_create_lojista_usuarios_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lojista_usuarios}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lojista_usuarios}}');
    }
}
