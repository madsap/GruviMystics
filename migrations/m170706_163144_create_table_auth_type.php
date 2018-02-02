<?php

use \yii\db\Migration;

class m170706_163144_create_table_auth_type extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%user_auth_type}}',
            [
                'id'                => $this->primaryKey(11),
                'registrationType'  => 'ENUM("email", "facebook") NOT NULL',
                'userId'            => $this->integer(11)->notNull()->defaultValue(0),
                'socialNetworkId'   => $this->string(150)->notNull()->defaultValue(''),
                'email'             => $this->string(150)->notNull()->defaultValue(''),
                'password'          => $this->char(32)->notNull()->defaultValue(''),
                'username'          => $this->string(150)->notNull()->defaultValue(''),
                'lastUpdatedTime'   => $this->timestamp()->notNull(),
                'createdAt'         => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_auth_type}}');
    }
}
