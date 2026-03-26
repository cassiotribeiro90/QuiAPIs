<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

Yii::setAlias('@app', dirname(__DIR__));

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
         // User para clientes/App
        'user' => [
            'identityClass' => 'app\models\app\AppUsuario',  // Clientes
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        
        // User para lojistas (nome diferente)
        'userLojista' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\lojista\LojistaUsuario',  // Lojistas
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'userGestor' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\gestor\GestorUsuario',  // Gestores
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'B226kftmhnPN6snNBVMWddi8P3nTJLK7',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser', // ESSENCIAL!
            ],
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

                // API App -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                // Auth
                'POST api/app/auth/login' => 'api/app/auth/login',
                'POST api/app/auth/cadastro' => 'api/app/auth/cadastro',
                'POST api/app/auth/refresh-token' => 'api/app/auth/refresh-token',
                'POST api/app/auth/logout' => 'api/app/auth/logout',
                'GET api/app/auth/me' => 'api/app/auth/me',


                // API Cliente (quiPede) - PÚBLICAS
                'GET api/app/lojas' => 'api/app/loja/index',
                'GET api/app/lojas/proximas' => 'api/app/loja/proximas',
                'GET api/app/lojas/<id:\d+>' => 'api/app/loja/view',
                'GET api/app/produtos' => 'api/app/produto/index',
                'GET api/app/produtos/<id:\d+>' => 'api/app/produto/view',
                'GET api/app/categorias' => 'api/app/categoria/index',

                // Endereços (requer autenticação)
                'GET api/app/enderecos' => 'api/app/endereco/index',
                'GET api/app/enderecos/<id:\d+>' => 'api/app/endereco/view',
                'POST api/app/enderecos' => 'api/app/endereco/create',
                'PUT api/app/enderecos/<id:\d+>' => 'api/app/endereco/update',
                'DELETE api/app/enderecos/<id:\d+>' => 'api/app/endereco/delete',
                'POST api/app/enderecos/<id:\d+>/padrao' => 'api/app/endereco/set-padrao',
                
                // Pedidos (requer autenticação)
                'GET api/app/pedidos' => 'api/app/pedido/index',
                'GET api/app/pedidos/<id:\d+>' => 'api/app/pedido/view',
                'POST api/app/pedidos' => 'api/app/pedido/create',
                'POST api/app/pedidos/<id:\d+>/cancelar' => 'api/app/pedido/cancelar',
                'GET api/app/pedidos/status/<status:\w+>' => 'api/app/pedido/por-status',

                // Avaliações
                'GET api/app/avaliacoes/loja/<lojaId:\d+>' => 'api/app/avaliacao/loja',
                'GET api/app/avaliacoes/produto/<produtoId:\d+>' => 'api/app/avaliacao/produto',
                'POST api/app/avaliacoes' => 'api/app/avaliacao/create',
                'PUT api/app/avaliacoes/<id:\d+>' => 'api/app/avaliacao/update',

                 // API Lojista -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
                'GET lojista/pedido' => 'api/lojista/pedido/index',
                'GET lojista/pedido/<id:\d+>' => 'api/lojista/pedido/view',
                'POST lojista/pedido' => 'api/lojista/pedido/create',
                'POST auth-lojista/create' => 'auth-lojista/create',
                'POST auth-lojista/login' => 'auth-lojista/login',
                'POST api/gestor/lojas/<id:\d+>/produtos' => 'api/gestor/loja/produtos',

                // API Gestor =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                // Usuarios do painel gestor
                'api/gestor/gestor-usuarios' => 'api/gestor/gestor-usuarios/index',
                'api/gestor/gestor-usuarios/<id:\d+>' => 'api/gestor/gestor-usuarios/view',
                'api/gestor/gestor-usuarios/login' => 'api/gestor/gestor-usuarios/login',
                'api/gestor/gestor-usuarios/logout' => 'api/gestor/gestor-usuarios/logout',
                'api/gestor/gestor-usuarios/me' => 'api/gestor/gestor-usuarios/me',
                'api/gestor/gestor-usuarios/create' => 'api/gestor/gestor-usuarios/create',
                'api/gestor/gestor-usuarios/update/<id:\d+>' => 'api/gestor/gestor-usuarios/update',
                'api/gestor/gestor-usuarios/delete/<id:\d+>' => 'api/gestor/gestor-usuarios/delete',
                'api/gestor/gestor-usuarios/refresh-token' => 'api/gestor/gestor-usuarios/refresh-token',
                'api/gestor/gestor-usuarios/check-token' => 'api/gestor/gestor-usuarios/check-token',

                // Dashboard do Gestor
                'api/gestor/dashboard' => 'api/gestor/dashboard/index',
                'api/gestor/dashboard/graficos' => 'api/gestor/dashboard/graficos',

                // Lojas do Gestor
                'api/gestor/lojas' => 'api/gestor/loja/index',
                'api/gestor/lojas/<id:\d+>' => 'api/gestor/loja/view',
                'api/gestor/lojas/create' => 'api/gestor/loja/create',
                'api/gestor/lojas/update/<id:\d+>' => 'api/gestor/loja/update',
                'api/gestor/lojas/update/<id:\d+>' => 'api/gestor/loja/update',
                'api/gestor/lojas/delete/<id:\d+>' => 'api/gestor/loja/delete',
                'api/gestor/lojas/options' => 'api/gestor/loja/options',

                // Rotas para Categorias
                'api/gestor/categorias' => 'api/gestor/categoria/index',
                'api/gestor/categorias/<id:\d+>' => 'api/gestor/categoria/view',
                'api/gestor/categorias/create' => 'api/gestor/categoria/create',
                'api/gestor/categorias/update/<id:\d+>' => 'api/gestor/categoria/update',
                'api/gestor/categorias/delete/<id:\d+>' => 'api/gestor/categoria/delete',
                'api/gestor/categorias/options' => 'api/gestor/categoria/options',

                // Rotas para Subcategorias
                'api/gestor/subcategorias' => 'api/gestor/subcategoria/index',
                'api/gestor/subcategorias/<id:\d+>' => 'api/gestor/subcategoria/view',
                'api/gestor/subcategorias/create' => 'api/gestor/subcategoria/create',
                'api/gestor/subcategorias/update/<id:\d+>' => 'api/gestor/subcategoria/update',
                'api/gestor/subcategorias/delete/<id:\d+>' => 'api/gestor/subcategoria/delete',
                'api/gestor/subcategorias/options' => 'api/gestor/subcategoria/options',
                'api/gestor/subcategorias/por-categoria/<id:\d+>' => 'api/gestor/subcategoria/por-categoria',
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
