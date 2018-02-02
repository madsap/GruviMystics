<?php

use yii\db\Migration;

class m170902_165852_create_table_chat extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%chat}}',
            [
                'id'                    => $this->bigPrimaryKey(),
                'customerId'            => $this->integer(11)->notNull()->defaultValue(0),
                'readerId'              => $this->integer(11)->notNull()->defaultValue(0),
                'chatRequestTime'       => $this->datetime()->notNull()->defaultExpression('NOW()'),
                'chatAcceptTime'        => $this->datetime()->null(),
                'chatEndTime'           => $this->datetime()->null(),
                'status'                => 'ENUM("Request", "Active", "Done", "Fail") NOT NULL',
                'createAt'              => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
        
		$this->addForeignKey(
			'fk_chat_customerId_to_user_id',
			'{{%chat}}',
			'customerId',
			'{{%user}}',
			'id'
		);
		$this->addForeignKey(
			'fk_chat_readerId_to_user_id',
			'{{%chat}}',
			'readerId',
			'{{%user}}',
			'id'
		);
        
        $this->createIndex('idx_status', '{{%chat}}', 'status', false);
    }

    public function safeDown()
    {
        $this->dropTable('{{%chat}}');
    }
    
}
