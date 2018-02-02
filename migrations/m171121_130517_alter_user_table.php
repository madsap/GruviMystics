<?php

use yii\db\Migration;

class m171121_130517_alter_user_table extends Migration
{
    public function safeUp()
    {
        //$this->dropColumn('{{%user}}', 'apiKey');
    }

    public function safeDown()
    {
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171121_130517_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
