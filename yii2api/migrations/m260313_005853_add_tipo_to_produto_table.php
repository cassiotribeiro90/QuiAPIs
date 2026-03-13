<?php

use yii\db\Migration;

class m260313_005853_add_tipo_to_produto_table extends Migration
{
    public function safeUp()
    {
        // Adiciona coluna 'tipo' como ENUM após subcategoria_id
        $this->addColumn('{{%produto}}', 'tipo', 
            "ENUM('simples', 'combo', 'personalizavel') NOT NULL DEFAULT 'simples' AFTER `subcategoria_id`"
        );
        
        $this->createIndex('idx-produto-tipo', '{{%produto}}', 'tipo');
    }

    public function safeDown()
    {
        $this->dropIndex('idx-produto-tipo', '{{%produto}}');
        $this->dropColumn('{{%produto}}', 'tipo');
    }
}