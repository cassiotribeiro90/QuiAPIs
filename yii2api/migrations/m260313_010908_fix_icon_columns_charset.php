<?php

use yii\db\Migration;

class m260313_010908_fix_icon_columns_charset extends Migration
{
    public function safeUp()
    {
        // 1. Categoria
        $this->execute("ALTER TABLE {{%categoria}} MODIFY `icone` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // 2. Subcategoria
        $this->execute("ALTER TABLE {{%subcategoria}} MODIFY `icone` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        echo "✅ Colunas de ícone alteradas para utf8mb4! Agora suportam emoji 🎉\n";
    }

    public function safeDown()
    {
        $this->execute("ALTER TABLE {{%categoria}} MODIFY `icone` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci");
        $this->execute("ALTER TABLE {{%subcategoria}} MODIFY `icone` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci");
    }
}