<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Chat';
?>
<div class="chat-container">
    <div class="row">
        <div class="col-md-3 user-list-container">
            <div class="panel panel-default">
                <div class="panel-heading">Пользователи</div>
                <div class="panel-body">
                    <div class="list-group" id="user-list">
                        <?php
                        /** @var $users \app\models\User[] $user */
                        foreach ($users as $user): ?>
                            <a href="#" class="list-group-item user-item"
                               data-userid="<?= $user->id ?>"
                               data-username="<?= Html::encode($user->username) ?>">
                                <?= Html::encode($user->username) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9 chat-area-container">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span id="chat-title">Выберите пользователя</span>
                </div>
                <div class="panel-body" id="chat-messages"></div>
                <div class="panel-footer">
                    <div class="input-group">
                        <input type="text" id="message-input" class="form-control" placeholder="Введите сообщение..." disabled>
                        <span class="input-group-btn">
                            <button id="send-button" class="btn btn-primary" disabled>Отправить</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentUser = {
            id: <?= $currentUser->id ?>,
            name: '<?= $currentUser->username ?>'
        };
        const wsProtocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
        const wsUrl = wsProtocol + window.location.hostname + ':8080';
        const ws = new WebSocket(wsUrl);
        let currentChatUserId = null;
        let pendingMessages = new Set();

        ws.onopen = function() {
            ws.send(JSON.stringify({
                type: 'auth',
                userId: currentUser.id
            }));
        };

        ws.onmessage = function(event) {
            console.log('Получено:', event.data);
            try {
                const data = JSON.parse(event.data);

                if (data.type === 'message') {
                    if (!pendingMessages.has(data.id)) {
                        addMessage(data.from, data.message, data.timestamp);
                    } else {
                        pendingMessages.delete(data.id);
                    }
                }
            } catch (e) {
                console.error('Ошибка', e);
            }
        };

        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                currentChatUserId = this.dataset.userid;
                document.getElementById('chat-title').textContent = `Пользователь ${this.dataset.username}`;
                document.getElementById('message-input').disabled = false;
                document.getElementById('send-button').disabled = false;

                loadChatHistory(currentChatUserId);
            });
        });

        document.getElementById('send-button').addEventListener('click', sendMessage);
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendMessage();
        });

        function loadChatHistory(userId) {
            fetch(`/site/chat-history?userId=${userId}`)
                .then(response => response.json())
                .then(messages => {
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.innerHTML = '';
                    messages.forEach(msg => {
                        addMessage(msg.user_id, msg.message, msg.created_at);
                    });
                    scrollToBottom();
                });
        }

        function addMessage(fromId, message, timestamp) {
            const isIncoming = fromId != currentUser.id;
            const messageClass = isIncoming ? 'incoming' : 'outgoing';
            const senderName = isIncoming ?
                (document.querySelector(`.user-item[data-userid="${fromId}"]`)?.dataset.username || 'Unknown') :
                'You';

            const date = new Date(typeof timestamp === 'number' ? timestamp * 1000 : timestamp);
            const time = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

            const messageElement = document.createElement('div');
            messageElement.className = `message ${messageClass}`;
            messageElement.innerHTML = `
                <div class="message-header">
                    <strong>${senderName}</strong>
                    <span class="time">${time}</span>
                </div>
                <div class="message-content">${message}</div>
            `;

            document.getElementById('chat-messages').appendChild(messageElement);
            scrollToBottom();
        }

        function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();

            if (message && currentChatUserId) {
                const tempId = Date.now();
                pendingMessages.add(tempId);

                ws.send(JSON.stringify({
                    type: 'private',
                    from: currentUser.id,
                    to: currentChatUserId,
                    message: message,
                    tempId: tempId
                }));

                input.value = '';
                input.focus();
            }
        }

        function scrollToBottom() {
            const chat = document.getElementById('chat-messages');
            chat.scrollTop = chat.scrollHeight;
        }
    });
</script>

<style>
    .chat-container {
        height: 80vh;
    }
    .user-list-container {
        height: 100%;
        padding-right: 0;
    }
    .chat-area-container {
        height: 100%;
        padding-left: 0;
    }
    #chat-messages {
        height: calc(100% - 100px);
        overflow-y: auto;
        padding: 10px;
    }
    .message {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 5px;
        max-width: 80%;
    }
    .message-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 0.9em;
    }
    .message-content {
        word-wrap: break-word;
    }
    .incoming {
        background-color: #f5f5f5;
        margin-right: auto;
    }
    .outgoing {
        background-color: #e1f5fe;
        margin-left: auto;
    }
    .time {
        color: #777;
        font-size: 0.8em;
    }
    .user-item {
        cursor: pointer;
    }
    .user-item:hover {
        background-color: #f0f0f0;
    }
    #message-input:disabled {
        background-color: #f9f9f9;
    }
</style>