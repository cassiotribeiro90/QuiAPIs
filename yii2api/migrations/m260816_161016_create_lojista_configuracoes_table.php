<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lojista_configuracoes}}`.
 * 
 * Tabela de configurações do lojista para o app QuiManda
 */
class m260816_161016_create_lojista_configuracoes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lojista_configuracoes}}', [
            'id' => $this->primaryKey(),
            'lojista_id' => $this->integer()->notNull()->comment('ID do lojista (store_usuario)'),
            
            // 🔥 Configurações de aceite automático
            'aceite_automatico' => $this->boolean()->defaultValue(0)->comment('Aceitar pedidos automaticamente'),
            'valor_minimo_aceite' => $this->decimal(10,2)->null()->comment('Valor mínimo para aceite automático'),
            'distancia_maxima_aceite' => $this->decimal(10,2)->null()->comment('Distância máxima em km para aceite automático'),
            
            // 🔥 Configurações de pausa programada
            'pausa_ativa' => $this->boolean()->defaultValue(0)->comment('Ativar pausa programada'),
            'pausa_inicio' => $this->time()->null()->comment('Horário de início da pausa'),
            'pausa_fim' => $this->time()->null()->comment('Horário de fim da pausa'),
            'pausa_dias_semana' => $this->json()->null()->comment('Dias da semana em pausa (["segunda","terca",...])'),
            
            // 🔥 Configurações de áudio e TTS
            'tts_ativo' => $this->boolean()->defaultValue(1)->comment('Ativar TTS (Text-to-Speech)'),
            'som_ativo' => $this->boolean()->defaultValue(1)->comment('Ativar sons de notificação'),
            'vibracao_ativa' => $this->boolean()->defaultValue(1)->comment('Ativar vibração'),
            'volume_tts' => $this->integer()->defaultValue(80)->comment('Volume do TTS (0-100)'),
            'tts_repetir_intervalo' => $this->integer()->defaultValue(0)->comment('Repetir TTS a cada X segundos (0 = não repetir)'),
            
            // 🔥 Configurações de notificações
            'notificacoes_ativas' => $this->boolean()->defaultValue(1)->comment('Notificações push ativas'),
            
            // 🔥 Configurações de interface
            'tema' => $this->string(20)->defaultValue('claro')->comment('Tema: claro, escuro, alto_contraste'),
            'fonte_tamanho' => $this->string(10)->defaultValue('medio')->comment('Tamanho fonte: pequeno, medio, grande'),
            
            // 🔥 Timestamps
            'criado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'atualizado_em' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // 🔥 Índices e Chaves Estrangeiras
        $this->createIndex('idx_lojista_configuracoes_lojista', '{{%lojista_configuracoes}}', 'lojista_id');
        $this->addForeignKey(
            'fk_lojista_configuracoes_lojista',
            '{{%lojista_configuracoes}}',
            'lojista_id',
            '{{%store_usuario}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_lojista_configuracoes_lojista', '{{%lojista_configuracoes}}');
        $this->dropTable('{{%lojista_configuracoes}}');
    }
}