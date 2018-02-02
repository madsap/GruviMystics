<?php

use yii\db\Migration;

class m171121_121233_alter_user_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }
        
        $this->addColumn('{{%user}}', 'apiKey', $this->string(500)->notNull()->defaultValue(''));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'apiKey');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171121_121233_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
