<?php

use \yii\db\Migration;

class m170706_161821_create_table_user extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%user}}',
            [
                'id'                => $this->primaryKey(),
                'role'              => 'ENUM("user", "reader", "admin") NOT NULL',
                'registrationType'  => 'ENUM("email", "facebook") NOT NULL',
                'email'             => $this->string(250)->defaultValue('')->notNull(),
                'firstName'         => $this->string(250)->notNull()->defaultValue(''),
                'lastName'          => $this->string(250)->notNull()->defaultValue(''),
                'dob'               => $this->date()->null()->defaultExpression('NULL')->comment('Date Of Birth'),
                'status'            => 'ENUM("active", "inactive", "deleted", "banned") NOT NULL DEFAULT "active"',
                'createAt'          => $this->timestamp()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );

        $this->createIndex('idx_email', '{{%user}}', 'email', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
