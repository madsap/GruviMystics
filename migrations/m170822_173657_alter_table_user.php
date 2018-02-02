<?php

use yii\db\Migration;

class m170822_173657_alter_table_user extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }
		
		$this->alterColumn("{{%user}}", "activity", 'ENUM("Disabled", "Offline", "Online", "Session") NOT NULL');
    }

    public function safeDown()
    {
        $this->alterColumn("{{%user}}", "activity", 'ENUM("Offline", "Online", "Session") NOT NULL');
    }

}
