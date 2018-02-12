<?php

use yii\db\Migration;

class m180209_211622_add_display_name_to_user_table extends Migration
{
    public function safeUp()
    {
       $this->addColumn('{{%user}}', 'displayname', $this->string(64)->null());

    }

    public function safeDown()
    {
        $this->dropColumn("{{%user}}", "displayname");
    }

}
