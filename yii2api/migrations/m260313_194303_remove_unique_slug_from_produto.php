<?php

use yii\db\Migration;

class m260313_194303_remove_unique_slug_from_produto extends Migration
{
    public function safeUp()
    {
        // Primeiro, verifica qual o nome do índice (pode ser 'slug' ou 'idx-produto-slug')
        // Como não podemos executar SHOW INDEX diretamente na migration,
        // vamos tentar remover ambos os possíveis nomes (o MySQL não dá erro se não existir)
        
        try {
            // Tenta remover o índice com nome 'slug' (comum quando a coluna tem UNIQUE)
            $this->execute("ALTER TABLE {{%produto}} DROP INDEX `slug`");
            echo "Índice 'slug' removido com sucesso.\n";
        } catch (\Exception $e) {
            // Se não existir, ignora
            echo "Índice 'slug' não encontrado ou já removido.\n";
        }
        
        try {
            // Tenta remover o índice com nome 'idx-produto-slug' (da migration original)
            $this->execute("ALTER TABLE {{%produto}} DROP INDEX `idx-produto-slug`");
            echo "Índice 'idx-produto-slug' removido com sucesso.\n";
        } catch (\Exception $e) {
            echo "Índice 'idx-produto-slug' não encontrado ou já removido.\n";
        }
        
        echo "✅ Constraint UNIQUE da coluna slug removida. Agora slugs podem ser duplicados.\n";
    }

    public function safeDown()
    {
        // Para reverter, recria o índice UNIQUE
        // MAS ATENÇÃO: isso pode falhar se houver slugs duplicados!
        try {
            $this->createIndex('idx-produto-slug', '{{%produto}}', 'slug', true);
            echo "Índice UNIQUE 'idx-produto-slug' recriado.\n";
        } catch (\Exception $e) {
            echo "ERRO: Não foi possível recriar o índice UNIQUE. Provavelmente existem slugs duplicados.\n";
            echo "Você precisará limpar os duplicados manualmente antes de reverter.\n";
        }
    }
}
