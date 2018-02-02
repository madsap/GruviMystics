<?php

use \yii\db\Migration;

class m170706_161004_create_table_session extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%user_session}}',
            [
                'id'        => $this->string()->notNull(),
                'expire'    => $this->integer(),
                'data'      => $this->binary(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('pk', '{{%user_session}}', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_session}}');
    }
}
