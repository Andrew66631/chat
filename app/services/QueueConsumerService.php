<?php

namespace app\services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use yii\base\Component;

class QueueConsumerService extends Component
{
    private $host;
    private $port;
    private $user;
    private $pass;

    public function __construct($host = 'rabbitmq', $port = 5672, $user = 'guest', $pass = 'guest')
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        parent::__construct();
    }

    private function transformMessage(string $message): array
    {
        return [
            'original' => $message,
            'length' => strlen($message),
            'timestamp' => time(),
            'processed' => strtoupper($message)
        ];
    }

    private function displayMessage(array $transformed): void
    {
        echo "Processed message:\n";
        print_r($transformed);
    }

    public function getMessage(string $queue = 'default_queue'): ?array
    {
        $connection = new AMQPStreamConnection(
            $this->host, // передаем как строку
            $this->port, // передаем как число
            $this->user, // передаем как строку
            $this->pass  // передаем как строку
        );
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);

        $message = $channel->basic_get($queue);

        if ($message) {
            $body = $message->body;
            $transformed = $this->transformMessage($body);
            $this->displayMessage($transformed);
            $channel->basic_ack($message->delivery_info['delivery_tag']);

            $channel->close();
            $connection->close();

            return $transformed;
        }

        $channel->close();
        $connection->close();

        return null;
    }
}