<?php

use yii\db\Migration;

class m260822_022737_add_device_id_to_gestor_usuario extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 🔥 ADICIONA A COLUNA device_id
        $this->addColumn('gestor_usuario', 'device_id', $this->string(255)->null());
        
        // 🔥 CRIA ÍNDICE PARA BUSCA RÁPIDA
        $this->createIndex('idx_gestor_usuario_device_id', 'gestor_usuario', 'device_id');
        
        echo "✅ Coluna 'device_id' adicionada à tabela gestor_usuario com sucesso!\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // 🔥 REMOVE O ÍNDICE
        $this->dropIndex('idx_gestor_usuario_device_id', 'gestor_usuario');
        
        // 🔥 REMOVE A COLUNA
        $this->dropColumn('gestor_usuario', 'device_id');
        
        echo "✅ Coluna 'device_id' removida da tabela gestor_usuario com sucesso!\n";
        
        return true;
    }
}