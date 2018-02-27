<?php

use yii\db\Migration;

class m180227_024837_add_notes_field_to_md_user_relation extends Migration
{
    public function safeUp()
    {

       $this->addColumn('{{%user_relation}}', 'notes', $this->text()->null());
    }

    public function safeDown()
    {
        $this->dropColumn("{{%user_relation}}", "notes");
    }

}
