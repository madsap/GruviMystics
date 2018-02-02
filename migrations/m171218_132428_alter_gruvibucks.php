<?php

use yii\db\Migration;

class m171218_132428_alter_gruvibucks extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%gruvi_bucks}}', 'paypalTransaction', $this->string(500)->null());//timestamp new_data_type
    }

    public function safeDown()
    {
       $this->alterColumn('{{%gruvi_bucks}}', 'paypalTransaction', $this->string(14)->null());//timestamp new_data_type
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171218_132428_alter_gruvibucks cannot be reverted.\n";

        return false;
    }
    */
}
