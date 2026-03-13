<?php

use yii\db\Migration;

class m260313_010028_create_atributo_categoria_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%atributo_categoria}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(100)->notNull(),
            'descricao' => $this->string(255)->null(),
            'tipo_selecao' => "ENUM('unica', 'multipla', 'quantidade', 'fracionado') NOT NULL DEFAULT 'unica'",
            'obrigatorio' => $this->boolean()->defaultValue(true),
            'minimo' => $this->integer()->defaultValue(1),
            'maximo' => $this->integer()->null(),
            'icone' => $this->string(50)->null(),
            'ordem' => $this->integer()->defaultValue(0),
            'ativo' => $this->boolean()->defaultValue(true),
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx-atributo_categoria-ativo', '{{%atributo_categoria}}', 'ativo');
        $this->createIndex('idx-atributo_categoria-ordem', '{{%atributo_categoria}}', 'ordem');
    }

    public function safeDown()
    {
        $this->dropTable('{{%atributo_categoria}}');
    }
}