<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lojista_lojas}}`.
 * Relacionamento: 1 lojista (usuário) → N lojas
 * Uma loja pode ter vários usuários (proprietário, gerentes, etc)
 */
class m260302_121311_create_lojista_lojas_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%lojista_lojas}}', [
            // ========== CHAVE PRIMÁRIA ==========
            'id' => $this->primaryKey(),
            
            // ========== IDENTIFICAÇÃO ==========
            'nome' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'descricao' => $this->text()->null(),
            
            // ========== CATEGORIA ==========
            'categoria' => $this->string(100)->notNull(),
            
            // ========== MÍDIA ==========
            'logo' => $this->string(500)->null(),
            'capa' => $this->string(500)->null(),
            
            // ========== AVALIAÇÃO ==========
            'nota_media' => $this->decimal(2,1)->defaultValue(0),
            'total_avaliacoes' => $this->integer()->defaultValue(0),
            
            // ========== ENTREGA ==========
            'tempo_entrega_min' => $this->integer()->notNull()->comment('minutos'),
            'tempo_entrega_max' => $this->integer()->notNull()->comment('minutos'),
            'taxa_entrega' => $this->decimal(10,2)->defaultValue(0),
            'pedido_minimo' => $this->decimal(10,2)->defaultValue(0),
            
            // ========== ENDEREÇO ==========
            'cep' => $this->string(9)->notNull(),
            'logradouro' => $this->string(255)->notNull(),
            'numero' => $this->string(20)->notNull(),
            'complemento' => $this->string(255)->null(),
            'bairro' => $this->string(100)->notNull(),
            'cidade' => $this->string(100)->notNull(),
            'uf' => $this->string(2)->notNull(),
            'latitude' => $this->decimal(10,8)->null(),
            'longitude' => $this->decimal(11,8)->null(),
            
            // ========== CONTATO ==========
            'telefone' => $this->string(20)->notNull(),
            'whatsapp' => $this->string(20)->null(),
            'email' => $this->string(255)->null(),
            'instagram' => $this->string(255)->null(),
            'site' => $this->string(255)->null(),
            
            // ========== HORÁRIOS ==========
            'horario_funcionamento' => $this->json()->null()->comment('ex: {"segunda": "08-18", "terca": "08-18"}'),
            
            // ========== STATUS ==========
            'status' => "ENUM('ativo', 'inativo', 'fechado', 'revisao') NOT NULL DEFAULT 'revisao'",
            'verificado' => $this->boolean()->defaultValue(false),
            'destaque' => $this->boolean()->defaultValue(false),
            
            // ========== CONFIGURAÇÕES ==========
            'configuracoes' => $this->json()->null()->comment('configurações específicas da loja'),
            
            // ========== METADADOS ==========
            'metadata' => $this->json()->null(),
            
            // ========== TIMESTAMPS ==========
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ], $this->getTableOptions());

        // ========== ÍNDICES ==========
        $this->createIndex('idx-lojista_lojas-slug', '{{%lojista_lojas}}', 'slug');
        $this->createIndex('idx-lojista_lojas-categoria', '{{%lojista_lojas}}', 'categoria');
        $this->createIndex('idx-lojista_lojas-status', '{{%lojista_lojas}}', 'status');
        $this->createIndex('idx-lojista_lojas-nota_media', '{{%lojista_lojas}}', 'nota_media');
        $this->createIndex('idx-lojista_lojas-destaque', '{{%lojista_lojas}}', 'destaque');
        $this->createIndex('idx-lojista_lojas-cidade', '{{%lojista_lojas}}', 'cidade');
        
        // ========== COMENTÁRIO ==========
        $this->addCommentOnTable('{{%lojista_lojas}}', 'Lojas cadastradas no sistema (para usuários lojistas)');
    }

    public function safeDown()
    {
        $this->dropTable('{{%lojista_lojas}}');
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