<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=mysql;dbname=apis_db',
    'username' => 'app_user',
    'password' => 'app123',
    'charset' => 'utf8',
    
    // Cache de schema (opcional, bom para produção)
    'enableSchemaCache' => YII_ENV_DEV,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];