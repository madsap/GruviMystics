<?php

use yii\db\Migration;

class m180328_014818_add_is_deleted_field_to_users extends Migration
{
    public function safeUp()
    {
       $this->addColumn('{{%user}}', 'is_deleted', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 0');
    }

    public function safeDown()
    {
        echo "m180328_014818_add_is_deleted_field_to_users cannot be reverted.\n";
        $this->dropColumn("{{%user}}", "notes");
    }

}
