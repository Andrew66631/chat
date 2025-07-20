<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'yii2-chat',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '8d7a6e5f4c3b2a1f0e9d8c7b6a5f4e3d2c1b0a9f8e7d6c5b4a3f2e1d0c9b8a7',
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
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@app/views',
                ],
            ],
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
                'chat' => 'chat/index',
                'chat/history' => 'chat/history',

                // Новые правила для RabbitMQ
                'queue/send' => 'queue-producer/send-custom',
                'queue/receive' => 'queue-consumer/receive',
                'queue/receive/<queue:\w+>' => 'queue-consumer/receive-from-queue',

                // API endpoints
                'api/queue/send' => 'queue-producer/send',
                'api/queue/receive' => 'queue-consumer/receive',
            ],
        ],
    ],
    'container' => [
        'singletons' => [
            'app\services\QueueProducerService' => function() {
                return new \app\services\QueueProducerService(
                    Yii::$app->params['rabbitmq']['host'],
                    Yii::$app->params['rabbitmq']['port'],
                    Yii::$app->params['rabbitmq']['user'],
                    Yii::$app->params['rabbitmq']['pass']
                );
            },
            'app\services\QueueConsumerService' => function() {
                return new \app\services\QueueConsumerService(
                    Yii::$app->params['rabbitmq']['host'],
                    Yii::$app->params['rabbitmq']['port'],
                    Yii::$app->params['rabbitmq']['user'],
                    Yii::$app->params['rabbitmq']['pass']
                );
            },
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
    ];
}

return $config;