<?php
return [
    'id' => 'yii2-chat-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['chat'],
                    'logFile' => '@runtime/logs/chat.log',
                ],
            ],
        ],
        'chatWebSocketHandler' => [
            'class' => 'app\components\ChatWebSocketHandler',
            'authService' => ['class' => 'app\services\AuthService'],
            'messageService' => ['class' => 'app\services\MessageService'],
            'senderService' => ['class' => 'app\services\SenderService'],
        ],
    ],
    'controllerMap' => [
        'chat-server' => 'app\commands\ChatServerController',
        'test-message' => 'app\commands\TestMessageCommand',

    ],
];