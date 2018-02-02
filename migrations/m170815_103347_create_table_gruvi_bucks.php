<?php

use yii\db\Migration;

class m170815_103347_create_table_gruvi_bucks extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%gruvi_bucks}}',
            [
                'id'                    => $this->bigPrimaryKey(),
                'userId'                => $this->integer(11)->notNull(),
                'creditCardId'          => $this->bigInteger(11)->null(),
                'stripeTransaction'     => $this->char(64)->null(),
                'amount'                => $this->decimal(10,2)->notNull()->defaultValue("0.00"),
                'log'                   => $this->text()->null(),
                'status'                => 'ENUM("approved", "declined") NOT NULL DEFAULT "approved"',
                'createAt'              => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
        
		$this->addForeignKey(
			'fk_gruvi_bucks_userId_to_user_id',
			'{{%gruvi_bucks}}',
			'userId',
			'{{%user}}',
			'id'
		);
        
		$this->addForeignKey(
			'fk_gruvi_bucks_creditCardId_to_card_id',
			'{{%gruvi_bucks}}',
			'creditCardId',
			'{{%user_credit_card}}',
			'id'
		);
        
        $this->createIndex('idx_status', '{{%gruvi_bucks}}', 'status', false);
    }

    public function safeDown()
    {
        $this->dropTable('{{%gruvi_bucks}}');
    }

}
