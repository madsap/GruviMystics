<?php

use yii\db\Migration;

class m170803_180523_alter_table_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'activity', 'ENUM("Offline", "Online", "Session") NOT NULL DEFAULT "Offline"');
        $this->addColumn('{{%user}}', 'activity_update', 'DATETIME NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'activity');
        $this->dropColumn('{{%user}}', 'activity_update');
    }
}
