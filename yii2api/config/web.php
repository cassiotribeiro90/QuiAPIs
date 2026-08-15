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
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $request = Yii::$app->request;
                
                // Headers CORS para todas as respostas
                $response->headers->set('Access-Control-Allow-Origin', '*');
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                $response->headers->set('Access-Control-Allow-Headers', '*');
                
                // Responder imediatamente a requisições OPTIONS (preflight CORS)
                if ($request->getMethod() === 'OPTIONS') {
                    $response->statusCode = 200;
                    $response->data = null;
                    $event->handled = true;
                }
            },
        ],
        // ==================== CONFIGURAÇÃO DE USUÁRIOS ====================
        // Usuário padrão (para APP - clientes)
        'user' => [
            'identityClass' => 'app\models\api\app\Usuario',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        
        // Componentes adicionais para outros tipos de usuário (usando nome diferente)
        'userLojista' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\api\lojista\LojistaUsuario',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        
        'userGestor' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\api\gestor\GestorUsuario',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        
        // ==================== REQUEST ====================
        'request' => [
            'cookieValidationKey' => 'B226kftmhnPN6snNBVMWddi8P3nTJLK7',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        
        // ==================== CACHE ====================
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        
        // ==================== ERROR HANDLER ====================
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        // ==================== MAILER ====================
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        
        // ==================== LOG ====================
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        
        // ==================== DATABASE ====================
        'db' => $db,
        
        // ==================== URL MANAGER ====================
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                // ==================== API APP (Cliente) - PÚBLICAS ====================
                
                // Auth
                'POST api/app/auth/login' => 'api/app/auth/login',
                'POST api/app/auth/cadastro' => 'api/app/auth/cadastro',
                'POST api/app/auth/refresh-token' => 'api/app/auth/refresh-token',
                'POST api/app/auth/logout' => 'api/app/auth/logout',
                'GET api/app/auth/me' => 'api/app/auth/me',
                'POST api/app/auth/me' => 'api/app/auth/update-me',
                'POST api/app/auth/phone' => 'api/app/auth/phone',
                'POST api/app/auth/verify-otp' => 'api/app/auth/verify-otp',
                'POST api/app/auth/social' => 'api/app/auth/social',
                'POST api/app/auth/convidado' => 'api/app/auth/convidado',
                'POST api/app/auth/update-telefone' => 'api/app/auth/update-telefone',
                'POST api/app/auth/confirm-update-telefone' => 'api/app/auth/confirm-update-telefone',

                // Carrinho
                'GET api/app/carrinho' => 'api/app/carrinho/index',
                'PUT api/app/carrinho/atualizar' => 'api/app/carrinho/atualizar',
                'POST api/app/carrinho/limpar' => 'api/app/carrinho/limpar',
                'POST api/app/carrinho/calcular' => 'api/app/carrinho/calcular',
                'GET api/app/carrinho/resumo' => 'api/app/carrinho/resumo',
                'GET api/app/carrinho/verificar-loja' => 'api/app/carrinho/verificar-loja',

                // CadastroController
                'POST api/app/cadastro/validar-etapa1' => 'api/app/cadastro/validar-etapa1',
                'POST api/app/cadastro/cadastrar' => 'api/app/cadastro/cadastrar',
                'GET api/app/cadastro/buscar-cep' => 'api/app/cadastro/buscar-cep',

                // LocalizacaoController
                'GET api/app/localizacao/geocodificar' => 'api/app/localizacao/geocodificar',
                'GET api/app/localizacao/buscar-endereco' => 'api/app/localizacao/buscar-endereco',
                'POST api/app/localizacao/confirmar-endereco' => 'api/app/localizacao/confirmar-endereco',
                
                // Lojas - Listagem
                'GET api/app/lojas' => 'api/app/loja/index',
                'GET api/app/loja' => 'api/app/loja/index',
                
                // ==================== LOJA HOME (DETALHES) ====================
                // Suporta: /loja-home?id=1, /loja-home/1, /loja-home?loja_id=1
                'GET api/app/loja-home' => 'api/app/loja-home/index',
                'GET api/app/loja-home/<id:\d+>' => 'api/app/loja-home/index',

                'GET api/app/produto-detail' => 'api/app/produto-detail/detalhe',
                'GET api/app/produto-detail/<id:\d+>' => 'api/app/produto-detail/detalhe',
                
                // Lojas - Próximas
                'GET api/app/lojas/proximas' => 'api/app/loja/proximas',
                'GET api/app/loja/proximas' => 'api/app/loja/proximas',
                
                // Produtos - Busca na loja (suporta /loja/1/search e /loja/search?id=1)
                'GET api/app/loja/<id:\d+>/search' => 'api/app/loja/search',
                'GET api/app/loja/search' => 'api/app/loja/search',
                
                // Categorias da loja
                'GET api/app/loja/<id:\d+>/categorias' => 'api/app/loja/categorias',
                'GET api/app/loja/categorias' => 'api/app/loja/categorias',
                
                // Avaliações da loja
                'GET api/app/loja/<id:\d+>/avaliacoes' => 'api/app/loja/avaliacoes',
                'GET api/app/loja/avaliacoes' => 'api/app/loja/avaliacoes',
                
                // Produtos gerais
                'GET api/app/produtos' => 'api/app/produto/index',
                'GET api/app/produtos/<id:\d+>' => 'api/app/produto/view',
                'POST api/app/produtos' => 'api/app/produto/create',
                
                // Categorias gerais
                'GET api/app/categorias' => 'api/app/categoria/index',
                                
                // Enderecos
                'GET api/app/enderecos' => 'api/app/endereco/index',
                'POST api/app/enderecos' => 'api/app/endereco/create',
                'PUT api/app/enderecos/<id:\d+>' => 'api/app/endereco/update',
                'DELETE api/app/enderecos/<id:\d+>' => 'api/app/endereco/delete',
                'GET api/app/enderecos/<id:\d+>' => 'api/app/endereco/view',
                'POST api/app/enderecos/buscar-cep' => 'api/app/endereco/buscar-cep',
                'PUT api/app/enderecos/<id:\d+>/set-padrao' => 'api/app/endereco/set-padrao',
                
                // Auth Convidado
                'POST api/app/auth/convidado' => 'api/app/auth/convidado',
                
                // Pedidos (App) – corrigir/adicionar
                'POST api/app/pedido/criar' => 'api/app/pedido/criar',
                'POST api/app/pedido/calcular-frete' => 'api/app/pedido/calcular-frete',
                'GET api/app/pedido/historico' => 'api/app/pedido/historico',
                'GET api/app/pedido/view/<id:\d+>' => 'api/app/pedido/view',
                'GET api/app/pedido/view' => 'api/app/pedido/view',
                'POST api/app/pedido/cancelar' => 'api/app/pedido/cancelar',
                
                // Avaliações
                'GET api/app/avaliacoes/loja/<lojaId:\d+>' => 'api/app/avaliacao/loja',
                'GET api/app/avaliacoes/produto/<produtoId:\d+>' => 'api/app/avaliacao/produto',
                'POST api/app/avaliacoes' => 'api/app/avaliacao/create',
                'PUT api/app/avaliacoes/<id:\d+>' => 'api/app/avaliacao/update',
                
                // ==================== API LOJISTA ====================
                'GET lojista/pedido' => 'api/lojista/pedido/index',
                'GET lojista/pedido/<id:\d+>' => 'api/lojista/pedido/view',
                'POST lojista/pedido' => 'api/lojista/pedido/create',
                'POST auth-lojista/create' => 'auth-lojista/create',
                'POST auth-lojista/login' => 'auth-lojista/login',
                'POST api/gestor/lojas/<id:\d+>/produtos' => 'api/gestor/loja/produtos',
                
                // ==================== API GESTOR ====================
                // Usuários do painel gestor
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
                
                // Dashboard
                'api/gestor/dashboard' => 'api/gestor/dashboard/index',
                'api/gestor/dashboard/graficos' => 'api/gestor/dashboard/graficos',
                
                // Lojas do Gestor
                'api/gestor/lojas' => 'api/gestor/loja/index',
                'api/gestor/lojas/<id:\d+>' => 'api/gestor/loja/view',
                'api/gestor/lojas/create' => 'api/gestor/loja/create',
                'api/gestor/lojas/update/<id:\d+>' => 'api/gestor/loja/update',
                'api/gestor/lojas/delete/<id:\d+>' => 'api/gestor/loja/delete',
                'api/gestor/lojas/options' => 'api/gestor/loja/options',
                
                // Categorias
                'api/gestor/categorias' => 'api/gestor/categoria/index',
                'api/gestor/categorias/<id:\d+>' => 'api/gestor/categoria/view',
                'api/gestor/categorias/create' => 'api/gestor/categoria/create',
                'api/gestor/categorias/update/<id:\d+>' => 'api/gestor/categoria/update',
                'api/gestor/categorias/delete/<id:\d+>' => 'api/gestor/categoria/delete',
                'api/gestor/categorias/options' => 'api/gestor/categoria/options',
                
                // Subcategorias
                'api/gestor/subcategorias' => 'api/gestor/subcategoria/index',
                'api/gestor/subcategorias/<id:\d+>' => 'api/gestor/subcategoria/view',
                'api/gestor/subcategorias/create' => 'api/gestor/subcategoria/create',
                'api/gestor/subcategorias/update/<id:\d+>' => 'api/gestor/subcategoria/update',
                'api/gestor/subcategorias/delete/<id:\d+>' => 'api/gestor/subcategoria/delete',
                'api/gestor/subcategorias/options' => 'api/gestor/subcategoria/options',
                'api/gestor/subcategorias/por-categoria/<id:\d+>' => 'api/gestor/subcategoria/por-categoria',
                
                // Lojistas (Store Usuários)
                'api/gestor/store-usuarios' => 'api/gestor/store-usuario/index',
                'api/gestor/store-usuarios/<id:\d+>' => 'api/gestor/store-usuario/view',
                'api/gestor/store-usuarios/create' => 'api/gestor/store-usuario/create',
                'api/gestor/store-usuarios/update/<id:\d+>' => 'api/gestor/store-usuario/update',
                'api/gestor/store-usuarios/delete/<id:\d+>' => 'api/gestor/store-usuario/delete',
                'api/gestor/store-usuarios/options' => 'api/gestor/store-usuario/options',
                'api/gestor/store-usuarios/lojas-options' => 'api/gestor/store-usuario/lojas-options',


                // LOJISTA APP ---------------------------------------------------------------------------
                // 🔥 ROTAS DE AUTENTICAÇÃO DO LOJISTA
                'POST api/lojista/auth-lojista/phone' => 'api/lojista/auth-lojista/phone',
                'POST api/lojista/auth-lojista/verify-otp' => 'api/lojista/auth-lojista/verify-otp',
                'POST api/lojista/auth-lojista/login' => 'api/lojista/auth-lojista/login',
                'POST api/lojista/auth-lojista/refresh-token' => 'api/lojista/auth-lojista/refresh-token',
                'POST api/lojista/auth-lojista/logout' => 'api/lojista/auth-lojista/logout',
                'POST api/lojista/auth-lojista/create' => 'api/lojista/auth-lojista/create',
                

            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;