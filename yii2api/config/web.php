<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'B226kftmhnPN6snNBVMWddi8P3nTJLK7',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
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
            'enableStrictParsing' => false,
            'rules' => [
                // API Lojista
                'GET lojista/pedido' => 'api/lojista/pedido/index',
                'GET lojista/pedido/<id:\d+>' => 'api/lojista/pedido/view',
                'POST lojista/pedido' => 'api/lojista/pedido/create',
                
                // API App
                'GET app/produto' => 'api/app/produto/index',
                'GET app/produto/<id:\d+>' => 'api/app/produto/view',
                'POST app/produto' => 'api/app/produto/create',
                
                // API Gestor
                'GET gestor/relatorio' => 'api/gestor/relatorio/index',
                'GET gestor/relatorio/vendas' => 'api/gestor/relatorio/vendas',
                'GET gestor/relatorio/clientes' => 'api/gestor/relatorio/clientes',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'], // LIBERADO PARA TESTE (depois você restringe)
    ];
}

return $config;
