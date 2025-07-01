<?php
namespace app\commands;

use yii\console\Controller;
use app\services\MessageService;
use Yii;

class TestMessageCommand extends Controller
{
    public function actionIndex($senderId, $recipientId, $message)
    {
        /** @var MessageService $service */
        $service = Yii::createObject(MessageService::class);

        try {
            $result = $service->createMessage(
                (int)$senderId,
                (int)$recipientId,
                $message
            );

            echo "Сообщение доставлено\n";
            echo "ID: {$result->id}\n";
            echo "От: {$result->user_id}\n";
            echo "Кому: {$result->recipient_id}\n";
            echo "Сообщение: {$result->message}\n";
            echo "Дата: " . date('Y-m-d H:i:s', $result->created_at) . "\n";

            return 0;
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return 1;
        }
    }
}