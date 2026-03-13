<?php

use yii\db\Migration;

class m260313_194303_remove_unique_slug_from_produto extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260313_194303_remove_unique_slug_from_produto cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260313_194303_remove_unique_slug_from_produto cannot be reverted.\n";

        return false;
    }
    */
}
