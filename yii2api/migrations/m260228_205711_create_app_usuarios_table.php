<?php
use yii\db\Migration;

class m260228_205711_create_app_usuarios_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('app_usuarios', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull()->unique(),
            'senha_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32),
            'cpf' => $this->string(11)->notNull()->unique(),
            'data_nascimento' => $this->date(),
            'telefone' => $this->string(20),
            'status' => $this->smallInteger()->defaultValue(10),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('app_usuarios');
    }
}