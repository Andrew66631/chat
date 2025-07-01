<?php

use yii\db\Migration;

class m250630_145230_addRecipientField extends Migration
{
    public function safeUp()
    {
        $this->addColumn('chat_message', 'recipient_id', $this->integer()->notNull());

        $this->addForeignKey(
            'fk-chat_message-recipient_id',
            'chat_message',
            'recipient_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-chat_message-recipient_id', 'chat_message');
        $this->dropColumn('chat_message', 'recipient_id');
    }
}
