<?php

use yii\db\Migration;

class m170928_203741_alter_table_message extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }
        
        $this->addColumn("{{%message}}", "changeAt", "DATETIME");
        $this->createIndex('idx_changeAt', '{{%message}}', 'changeAt', false);
    }

    public function safeDown()
    {
        $this->dropColumn("%message", "changeAt");
    }

}
