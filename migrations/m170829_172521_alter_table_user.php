<?php

use yii\db\Migration;

class m170829_172521_alter_table_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'password_reset_token', 'VARCHAR(255) NULL');
        $this->createIndex('idx_password_reset_token', '{{%user}}', 'password_reset_token', true);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'password_reset_token');
    }

}
