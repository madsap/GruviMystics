<?php

use yii\db\Migration;

class m170806_182548_create_table_call extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%call}}',
            [
                'id'                    => $this->bigPrimaryKey(),
                'customerId'            => $this->integer(11)->notNull()->defaultValue(0),
                'readerId'              => $this->integer(11)->notNull()->defaultValue(0),
                'duration'              => $this->integer(11)->notNull()->defaultValue(0),
                'callConnectionTime'    => $this->datetime()->notNull()->defaultExpression('NOW()'),
                'callAnswerTime'        => $this->datetime()->null(),
                'callEndTime'           => $this->datetime()->null(),
                'status'                => 'ENUM("Connecting", "Conversation", "Done", "Fail") NOT NULL',
                'createAt'              => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
        
		$this->addForeignKey(
			'fk_call_customerId_to_user_id',
			'{{%call}}',
			'customerId',
			'{{%user}}',
			'id'
		);
		$this->addForeignKey(
			'fk_call_readerId_to_user_id',
			'{{%call}}',
			'readerId',
			'{{%user}}',
			'id'
		);
        
        $this->createIndex('idx_status', '{{%call}}', 'status', false);
    }

    public function safeDown()
    {
        $this->dropTable('{{%call}}');
    }

}
