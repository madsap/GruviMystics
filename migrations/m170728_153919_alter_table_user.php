<?php

use yii\db\Migration;

class m170728_153919_alter_table_user extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }
        
        $this->addColumn('{{%user}}', 'rate', $this->decimal(10, 2)->notNull()->defaultValue('0.0'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'rate');
    }
    
}
