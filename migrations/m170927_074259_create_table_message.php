<?php

use yii\db\Migration;

class m170927_074259_create_table_message extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%message}}',
            [
                'id'                    => $this->bigPrimaryKey(),
                'customerId'            => $this->integer(11)->notNull()->defaultValue(0),
                'readerId'              => $this->integer(11)->notNull()->defaultValue(0),
                'message'               => $this->text(),
                'status'                => 'ENUM("visible", "deleted", "banned") NOT NULL DEFAULT "visible"',
                'createAt'              => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
        
		$this->addForeignKey(
			'fk_message_customerId_to_user_id',
			'{{%message}}',
			'customerId',
			'{{%user}}',
			'id',
            'CASCADE',
            'CASCADE'
		);
        
		$this->addForeignKey(
			'fk_message_readerId_to_user_id',
			'{{%message}}',
			'readerId',
			'{{%user}}',
			'id',
            'CASCADE',
            'CASCADE'
		);
        
        $this->createIndex('idx_status', '{{%message}}', 'status', false);
    }

    public function safeDown()
    {
        $this->dropTable('{{%message}}');
    }

}
