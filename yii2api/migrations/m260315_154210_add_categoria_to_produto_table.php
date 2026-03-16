<?php

use yii\db\Migration;

class m260315_154210_add_categoria_to_produto_table extends Migration
{
    public function safeUp()
    {
        // Adicionar a coluna categoria_id
        $this->addColumn('{{%produto}}', 'categoria_id', $this->integer()->null());

        // Criar chave estrangeira para tabela categoria
        $this->addForeignKey(
            'fk-produto-categoria_id',
            '{{%produto}}',
            'categoria_id',
            '{{%categoria}}',
            'id',
            'SET NULL', // Se a categoria for deletada, o produto fica sem categoria
            'CASCADE'
        );

        // Criar índice para consultas por categoria
        $this->createIndex('idx-produto-categoria_id', '{{%produto}}', 'categoria_id');
    }

    public function safeDown()
    {
        // Remover a chave estrangeira, índice e coluna
        $this->dropForeignKey('fk-produto-categoria_id', '{{%produto}}');
        $this->dropIndex('idx-produto-categoria_id', '{{%produto}}');
        $this->dropColumn('{{%produto}}', 'categoria_id');
    }
}