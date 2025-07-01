<?php
namespace app\services;

use Ratchet\ConnectionInterface;
use yii\helpers\Json;
use yii\base\Component;
use app\models\ChatMessage;

class SenderService extends Component
{

    /**
     * @param ConnectionInterface $conn
     * @param array $data
     * @return void
     */
    public function send(ConnectionInterface $conn, array $data): void
    {
        $conn->send(Json::encode($data));
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $message
     * @return void
     */
    public function sendError(ConnectionInterface $conn, string $message): void
    {
        $this->send($conn, [
            'type' => 'error',
            'message' => $message
        ]);
    }

    /**
     * @param ConnectionInterface $conn
     * @param array $data
     * @return void
     */
    public function sendSuccess(ConnectionInterface $conn, array $data): void
    {
        $this->send($conn, array_merge($data, [
            'status' => 'success'
        ]));
    }

    /**
     * @param ChatMessage $message
     * @return array
     */
    public function prepareMessageResponse(ChatMessage $message): array
    {
        return [
            'type' => 'message',
            'id' => $message->id,
            'from' => $message->user_id,
            'to' => $message->recipient_id,
            'message' => $message->message,
            'timestamp' => $message->created_at
        ];
    }
}