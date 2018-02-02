<?php

use yii\db\Migration;

class m170928_130848_create_table_user_relation extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%user_relation}}',
            [
                'id'                    => $this->bigPrimaryKey(),
                'senderId'              => $this->integer(11)->notNull(),
                'recipientId'           => $this->integer(11)->notNull(),
                'messageId'             => $this->bigInteger()->null(),
                'action'                => 'ENUM("block", "follow", "like", "favorite") NULL',
                'createAt'              => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
        
		$this->addForeignKey(
			'fk_user_relation_senderId_to_user_id',
			'{{%user_relation}}',
			'senderId',
			'{{%user}}',
			'id',
            'CASCADE',
            'CASCADE'
		);
		$this->addForeignKey(
			'fk_user_relation_recipientId_to_user_id',
			'{{%user_relation}}',
			'recipientId',
			'{{%user}}',
			'id',
            'CASCADE',
            'CASCADE'
		);
		$this->addForeignKey(
			'fk_user_relation_messageId_to_message_id',
			'{{%user_relation}}',
			'messageId',
			'{{%message}}',
			'id',
            'SET NULL',
            'SET NULL'
		);
        
        $this->createIndex('idx_action', '{{%user_relation}}', 'action', false);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_relation}}');
    }

}
