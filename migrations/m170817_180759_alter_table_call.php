<?php

use yii\db\Migration;

class m170817_180759_alter_table_call extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'twilioCallId', 'VARCHAR(64) NULL');
        $this->createIndex('idx_twilioCallId', '{{%call}}', 'twilioCallId', false);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%call}}', 'twilioCallId');
    }

}
