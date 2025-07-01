<?php
namespace app\services;

use Ratchet\ConnectionInterface;
use app\models\User;
use yii\base\Component;

class AuthService extends Component
{
    /**
     * @var array
     */
    private array $connections = [];

    /**
     * @param ConnectionInterface $conn
     * @param array $data
     * @return int
     */
    public function authenticate(ConnectionInterface $conn, array $data): int
    {
        $userId = (int)($data['userId'] ?? 0);

        if (!User::findOne($userId)) {
            throw new \RuntimeException("Пользователь с ID- $userId не найден");
        }

        $this->connections[$userId] = $conn;
        return $userId;
    }

    /**
     * @param int $userId
     * @return ConnectionInterface|null
     */

    public function getConnection(int $userId): ?ConnectionInterface
    {
        return $this->connections[$userId] ?? null;
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function removeConnection(ConnectionInterface $conn): void
    {
        if ($userId = array_search($conn, $this->connections, true)) {
            unset($this->connections[$userId]);
        }
    }
}