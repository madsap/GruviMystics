<?php

use yii\db\Migration;

class m170724_141336_alter_table_user extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

       $this->addColumn('{{%user}}', 'username', $this->string(64)->null());
       $this->addColumn('{{%user}}', 'telephone', $this->string(32)->notNull()->defaultValue(''));
       $this->addColumn('{{%user}}', 'tagLine', $this->string(128)->notNull()->defaultValue(''));
       $this->addColumn('{{%user}}', 'description', $this->text()->null()->defaultExpression('NULL'));

       $this->createIndex('idx_username', '{{%user}}', 'username', true);
    }

    public function safeDown()
    {
        $this->dropColumn("{{%user}}", "username");
        $this->dropColumn("{{%user}}", "telephone");
        $this->dropColumn("{{%user}}", "tagLine");
        $this->dropColumn("{{%user}}", "description");
    }

}
