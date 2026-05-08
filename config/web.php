<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'encurtador',
    'name' => 'Encurtador URLs',
    'language' => 'pt-PT',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'JrC_UL1zfdklv_rXlrQ-L4xmLUqZcjz1',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'login' => 'site/login',
                'logout' => 'site/logout',
                'register' => 'site/register',
                'forgot-password' => 'site/forgot-password',
                'reset-password' => 'site/reset-password',
                'profile' => 'site/profile',
                'privacy' => 'site/privacy',
                'dashboard' => 'dashboard/index',
                'links' => 'short-url/index',
                'links/create' => 'short-url/create',
                'links/update/<id:\d+>' => 'short-url/update',
                'links/stats/<id:\d+>' => 'short-url/stats',
                'links/<id:\d+>' => 'short-url/view',
                'links/delete/<id:\d+>' => 'short-url/delete',
                'links/generate-qr/<id:\d+>' => 'short-url/generate-qr',
                'links/download-qr/<id:\d+>' => 'short-url/download-qr',
                'links/download-qr-svg/<id:\d+>' => 'short-url/download-qr-svg',
                'campaigns' => 'campaign/index',
                'campaigns/create' => 'campaign/create',
                'campaigns/update/<id:\d+>' => 'campaign/update',
                'campaigns/<id:\d+>' => 'campaign/view',
                'scanlog' => 'scanlog/index',
                'scanlog/export-csv' => 'scanlog/export-csv',
                'scanlog/<id:\d+>' => 'scanlog/view',
                'users' => 'user/index',
                'users/<id:\d+>' => 'user/view',
                'users/update/<id:\d+>' => 'user/update',
                // MUST be last — catch-all for short codes
                '<shortCode:[a-zA-Z0-9_-]+>' => 'redirect/go',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
