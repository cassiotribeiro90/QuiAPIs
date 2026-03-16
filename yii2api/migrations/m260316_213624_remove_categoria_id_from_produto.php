<?php
// migrations/m260316_210000_remove_categoria_id_from_produto.php

use yii\db\Migration;

/**
 * Remove a coluna categoria_id da tabela produto
 * A categoria agora será obtida através da subcategoria
 */
class m260316_213624_remove_categoria_id_from_produto extends Migration
{
    public function safeUp()
    {
        // Remove a chave estrangeira primeiro
        $this->dropForeignKey('fk-produto-categoria_id', '{{%produto}}');
        
        // Remove o índice
        $this->dropIndex('idx-produto-categoria_id', '{{%produto}}');
        
        // Remove a coluna
        $this->dropColumn('{{%produto}}', 'categoria_id');
        
        echo "    > Coluna categoria_id removida da tabela produto.\n";
    }

    public function safeDown()
    {
        // Adiciona a coluna de volta
        $this->addColumn('{{%produto}}', 'categoria_id', $this->integer()->null());
        
        // Recria o índice
        $this->createIndex(
            'idx-produto-categoria_id',
            '{{%produto}}',
            'categoria_id'
        );
        
        // Recria a chave estrangeira
        $this->addForeignKey(
            'fk-produto-categoria_id',
            '{{%produto}}',
            'categoria_id',
            '{{%categoria}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        
        echo "    > Coluna categoria_id adicionada novamente à tabela produto.\n";
    }
}