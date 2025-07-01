<?php
namespace app\commands;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use yii\console\Controller;
use app\components\ChatWebSocketHandler;

class ChatServerController extends Controller
{
    /**
     * @param $port
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function actionStart($port = 8080)
    {
        $handler = \Yii::createObject(ChatWebSocketHandler::class);

        echo "=================================\n";
        echo "Запуск сервера..\n";
        echo "Порт: {$port}\n";
        echo "PID: " . getmypid() . "\n";
        echo "=================================\n\n";

        $server = IoServer::factory(
            new HttpServer(
                new WsServer($handler)
            ),
            $port
        );

        echo "[".date('Y-m-d H:i:s')."] Сервер запущен {$port}\n";
        echo "Ожидание подключения...\n\n";

        $server->run();
    }
}