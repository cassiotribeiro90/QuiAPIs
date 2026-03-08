<?php

use yii\db\Migration;

class m260308_133054_create_loja_horario_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%loja_horario}}', [
            'id' => $this->primaryKey(),
            
            // ========== RELACIONAMENTO ==========
            'loja_id' => $this->integer()->notNull(),
            
            // ========== HORÁRIO ==========
            'dia_semana' => $this->integer()->notNull()->comment('0-6 (domingo a sábado)'),
            'abre' => $this->time()->notNull(),
            'fecha' => $this->time()->notNull(),
            
            // ========== STATUS ==========
            'fechado' => $this->boolean()->defaultValue(false),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->getTableOptions());

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-loja_horario-loja_id',
            '{{%loja_horario}}',
            'loja_id',
            '{{%loja}}',
            'id',
            'CASCADE'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-loja_horario-loja_id', '{{%loja_horario}}', 'loja_id');
        $this->createIndex('idx-loja_horario-dia_semana', '{{%loja_horario}}', 'dia_semana');
        $this->createIndex('idx-loja_horario-unico', '{{%loja_horario}}', ['loja_id', 'dia_semana'], true);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-loja_horario-loja_id', '{{%loja_horario}}');
        $this->dropTable('{{%loja_horario}}');
    }
    
    private function getTableOptions()
    {
        $driver = $this->db->driverName;
        if ($driver === 'mysql') {
            return 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        return null;
    }
}