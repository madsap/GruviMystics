<?php

use \yii\db\Migration;

class m170706_161350_create_table_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%log}}',
            [
                'id'        => $this->bigPrimaryKey(),
                'level'     => $this->integer(),
                'category'  => $this->string(),
                'log_time'  => $this->double(),
                'prefix'    => $this->text(),
                'message'   => $this->text(),
            ],
            $tableOptions
        );

        $this->createIndex('idx_log_level', '{{%log}}', 'level');
        $this->createIndex('idx_log_category', '{{%log}}', 'category');
    }

    public function safeDown()
    {
        $this->dropTable('{{%log}}');
    }
}
