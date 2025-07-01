<?php
namespace app\services;

use app\models\ChatMessage;
use app\models\User;
use yii\base\Component;
use yii\db\Exception;

class MessageService extends Component
{

    /**
     * @param int $senderId
     * @param int $recipientId
     * @param string $text
     * @return ChatMessage
     * @throws Exception
     */
    public function createMessage(int $senderId, int $recipientId, string $text): ChatMessage
    {
        $this->validateUsers($senderId, $recipientId);

        $message = new ChatMessage();
        $message->user_id = $senderId;
        $message->recipient_id = $recipientId;
        $message->message = $text;
        $message->created_at = time();

        if (!$message->save()) {
            $error = 'Ошибка сохранения сообщения' . print_r($message->errors, true);
            echo "[".date('Y-m-d H:i:s')."] ERROR: {$error}\n";
            throw new Exception($error);
        }

        echo "[".date('Y-m-d H:i:s')."] Сохранение сохранено ID: {$message->id}\n";
        return $message;
    }

    /**
     * @param int $senderId
     * @param int $recipientId
     * @return void
     */

    private function validateUsers(int $senderId, int $recipientId): void
    {
        if ($senderId === $recipientId) {
            throw new \InvalidArgumentException('Невозможно отправить сообщение самому себе');
        }

        if (!User::findOne($senderId)) {
            throw new \InvalidArgumentException("Отправитель $senderId не найден");
        }

        if (!User::findOne($recipientId)) {
            throw new \InvalidArgumentException("Получатель  $recipientId не найден");
        }
    }
}