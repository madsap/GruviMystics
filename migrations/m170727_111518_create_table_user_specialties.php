<?php

use yii\db\Migration;

class m170727_111518_create_table_user_specialties extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%user_specialty}}',
            [
                'id'                => $this->bigPrimaryKey(),
                'userId'            => $this->integer(11)->notNull()->defaultValue(0),
                'specialty'         => $this->string(64)->notNull()->defaultValue(''),
                'createAt'          => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
        
		$this->addForeignKey(
			'fk_user_specialty_userId_to_user_id',
			'{{%user_specialty}}',
			'userId',
			'{{%user}}',
			'id'
		);
        
        $this->createIndex('idx_specialty', '{{%user_specialty}}', 'specialty', false);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_specialty}}');
    }
}
