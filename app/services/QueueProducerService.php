<?php

namespace app\services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\base\Component;

class QueueProducerService extends Component
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

    private function validateMessage($message): ?string
    {
        if (empty($message)) {
            return 'Message cannot be empty';
        }

        if (!is_string($message)) {
            return 'Message must be a string';
        }

        if (strlen($message) > 1000) {
            return 'Message too long (max 1000 chars)';
        }

        return null;
    }

    public function sendMessage(string $message, string $queue = 'default_queue'): bool
    {
        if ($error = $this->validateMessage($message)) {
            throw new \InvalidArgumentException($error);
        }

        $connection = new AMQPStreamConnection(
            $this->host, // передаем как строку
            $this->port, // передаем как число
            $this->user, // передаем как строку
            $this->pass  // передаем как строку
        );
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);

        $msg = new AMQPMessage($message, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);
        $channel->basic_publish($msg, '', $queue);

        $channel->close();
        $connection->close();

        return true;
    }
}