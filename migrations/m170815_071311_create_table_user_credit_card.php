<?php

use yii\db\Migration;

class m170815_071311_create_table_user_credit_card extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%user_credit_card}}',
            [
                'id'                    => $this->bigPrimaryKey(),
                'userId'                => $this->integer(11)->null(),
                'token'                 => $this->char(128)->null(),
                'last4'                 => $this->smallInteger()->null(),
                'expiration'            => $this->char(7)->null(),
                'status'                => 'ENUM("active", "removed") NOT NULL DEFAULT "active"',
                'createAt'              => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
        
		$this->addForeignKey(
			'fk_user_credit_card_userId_to_user_id',
			'{{%user_credit_card}}',
			'userId',
			'{{%user}}',
			'id'
		);
        
        $this->createIndex('idx_status', '{{%user_credit_card}}', 'status', false);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_credit_card}}');
    }

}
