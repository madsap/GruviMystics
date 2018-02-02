<?php

use yii\db\Migration;

class m170823_165030_create_table_request_a_call extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%request_a_call}}',
            [
                'id'                    => $this->bigPrimaryKey(),
                'customerId'            => $this->integer(11)->notNull()->defaultValue(0),
                'readerId'              => $this->integer(11)->notNull()->defaultValue(0),
                'phone'                 => $this->char(32)->notNull()->defaultValue(''),
                'status'                => 'ENUM("New", "Accepted", "Declined") NOT NULL DEFAULT "New"',
                'createAt'              => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
        
		$this->addForeignKey(
			'fk_request_a_call_customerId_to_user_id',
			'{{%request_a_call}}',
			'customerId',
			'{{%user}}',
			'id'
		);
		$this->addForeignKey(
			'fk_request_a_call_readerId_to_user_id',
			'{{%request_a_call}}',
			'readerId',
			'{{%user}}',
			'id'
		);
        
        $this->createIndex('idx_status', '{{%request_a_call}}', 'status', false);
    }

    public function safeDown()
    {
        $this->dropTable('{{%request_a_call}}');
    }

}
