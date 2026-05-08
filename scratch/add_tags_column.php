<?php
define('YII_DEBUG', true);
define('YII_ENV', 'dev');
require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');
$config = require(__DIR__ . '/config/web.php');
(new yii\web\Application($config));

try {
    Yii::$app->db->createCommand("ALTER TABLE short_url ADD COLUMN tags VARCHAR(255) NULL AFTER notes")->execute();
    echo "Coluna 'tags' adicionada com sucesso.\n";
} catch (\Exception $e) {
    echo "Erro ou coluna já existe: " . $e->getMessage() . "\n";
}
