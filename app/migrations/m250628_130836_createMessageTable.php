<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%message}}`.
 */
class m250628_130836_createMessageTable extends Migration
{
    public function up()
    {
        $this->createTable('chat_message', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-chat_message-user_id',
            'chat_message',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('chat_message');
    }
}
