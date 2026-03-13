<?php

use yii\db\Migration;

class m260313_010343_create_regra_atributo_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%regra_atributo}}', [
            'id' => $this->primaryKey(),
            
            // ========== REGRA ==========
            'regra_tipo' => "ENUM('requer', 'bloqueia', 'sugere') NOT NULL",
            
            // ========== OPÇÃO AFETADA ==========
            'opcao_id' => $this->integer()->notNull(),
            
            // ========== OPÇÃO REQUERIDA/BLOQUEADA ==========
            'opcao_requerida_id' => $this->integer()->null(),
            
            // ========== CATEGORIA REQUERIDA (alternativa) ==========
            'categoria_requerida_id' => $this->integer()->null(),
            
            // ========== VALOR MÍNIMO/MAXIMO ==========
            'valor_min' => $this->integer()->null(),
            'valor_max' => $this->integer()->null(),
            
            // ========== MENSAGEM ==========
            'mensagem' => $this->string(255)->null(),
            
            // ========== TIMESTAMPS ==========
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // ========== CHAVES ESTRANGEIRAS ==========
        $this->addForeignKey(
            'fk-regra_atributo-opcao_id',
            '{{%regra_atributo}}',
            'opcao_id',
            '{{%atributo_opcao}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-regra_atributo-opcao_requerida_id',
            '{{%regra_atributo}}',
            'opcao_requerida_id',
            '{{%atributo_opcao}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-regra_atributo-categoria_requerida_id',
            '{{%regra_atributo}}',
            'categoria_requerida_id',
            '{{%atributo_categoria}}',
            'id',
            'SET NULL'
        );

        // ========== ÍNDICES ==========
        $this->createIndex('idx-regra_atributo-opcao_id', '{{%regra_atributo}}', 'opcao_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-regra_atributo-categoria_requerida_id', '{{%regra_atributo}}');
        $this->dropForeignKey('fk-regra_atributo-opcao_requerida_id', '{{%regra_atributo}}');
        $this->dropForeignKey('fk-regra_atributo-opcao_id', '{{%regra_atributo}}');
        $this->dropTable('{{%regra_atributo}}');
    }
}