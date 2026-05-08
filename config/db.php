<?php

// Database configuration for Yii2
// For Render Postgres, DB_DSN should look like: pgsql:host=dpg-xxxx.render.com;port=5432;dbname=encurtador
$dsn = getenv('DB_DSN') ?: 'mysql:host=localhost;dbname=encurtadorurls';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

return [
    'class' => 'yii\db\Connection',
    'dsn' => $dsn,
    'username' => $user,
    'password' => $pass,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
