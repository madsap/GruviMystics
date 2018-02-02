<?php

use yii\db\Migration;

class m171220_053528_alter_user_table_add_isMobile_flag extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'isMobile', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'isMobile');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171220_053528_alter_user_table_add_isMobile_flag cannot be reverted.\n";

        return false;
    }
    */
}
