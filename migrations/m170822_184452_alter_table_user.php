<?php

use yii\db\Migration;

class m170822_184452_alter_table_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'opt_voice', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 1');
        $this->addColumn('{{%user}}', 'opt_chat', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 1');
        $this->addColumn('{{%user}}', 'opt_request', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 0');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'opt_voice');
        $this->dropColumn('{{%user}}', 'opt_chat');
        $this->dropColumn('{{%user}}', 'opt_request');
    }

}
