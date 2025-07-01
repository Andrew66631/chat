<?php
namespace app\components;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use app\services\AuthService;
use app\services\MessageService;
use app\services\SenderService;
use yii\base\Component;
use yii\helpers\Json;

class ChatWebSocketHandler extends Component implements MessageComponentInterface
{
    private AuthService $authService;
    private MessageService $messageService;
    private SenderService $senderService;

    public function __construct(
        AuthService $authService,
        MessageService $messageService,
        SenderService $senderService,
        $config = []
    ) {
        parent::__construct($config);
        $this->authService = $authService;
        $this->messageService = $messageService;
        $this->senderService = $senderService;
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onOpen(ConnectionInterface $conn){}

    /**
     * @param ConnectionInterface $from
     * @param $msg
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = Json::decode($msg);

            match ($data['type']) {
                'auth' => $this->handleAuth($from, $data),
                'private' => $this->handlePrivateMessage($from, $data),
            };
        } catch (\Exception $e) {
            $this->senderService->sendError($from, $e->getMessage());
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @param array $data
     * @return void
     */
    protected function handleAuth(ConnectionInterface $conn, array $data)
    {
        $userId = $this->authService->authenticate($conn, $data);
        $this->senderService->sendSuccess($conn, [
            'type' => 'auth_success',
            'userId' => $userId
        ]);
    }

    /**
     * @param ConnectionInterface $from
     * @param array $data
     * @return void
     * @throws \yii\db\Exception
     */

    protected function handlePrivateMessage(ConnectionInterface $from, array $data)
    {
        $senderId = (int)$data['from'];
        $recipientId = (int)$data['to'];
        $messageText = trim($data['message']);

        $message = $this->messageService->createMessage($senderId, $recipientId, $messageText);

        $response = $this->senderService->prepareMessageResponse($message);
        $this->senderService->send($from, $response);

        if ($recipientConn = $this->authService->getConnection($recipientId)) {
            $this->senderService->send($recipientConn, $response);
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->authService->removeConnection($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}