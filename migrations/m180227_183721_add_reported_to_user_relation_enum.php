<?php

use yii\db\Migration;

class m180227_183721_add_reported_to_user_relation_enum extends Migration
{
    public function safeUp()
    {
       $this->alterColumn('{{%user_relation}}', 'action',  'ENUM("block", "report", "follow", "like", "favorite") NULL' );

    }

    public function safeDown()
    {
       $this->alterColumn('{{%user_relation}}', 'action',  'ENUM("block", "follow", "like", "favorite") NULL' );
    }

}
