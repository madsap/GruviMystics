<?php

use \yii\db\Migration;

class m170706_161659_create_table_file extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%file}}',
            [
                'id'                => $this->bigPrimaryKey(),
                'userId'            => $this->integer(11)->notNull()->defaultValue(0),
                'hashName'          => $this->string(100)->notNull()->defaultValue(''),
                'extension'         => $this->string(10)->notNull()->defaultValue(''),
                'tableName'         => 'ENUM("User") NOT NULL',
                'tableId'           => $this->integer(11)->notNull()->defaultValue(0),
                'order'             => $this->integer(3)->notNull()->defaultValue(0),
                'typeFile'          => 'ENUM("image", "video") NOT NULL',
                'localName'         => $this->string(200)->notNull()->defaultValue(''),
                'hashFile'          => $this->string(32)->notNull()->defaultValue(''),
                'size'              => $this->bigInteger()->notNull()->defaultValue(0),
                'mainCategoryName'  => 'ENUM("logo", "attachment") NOT NULL',
                'categoryName'      => 'ENUM("original", "small", "middle", "large") NOT NULL DEFAULT "original"',
                'url'               => $this->text()->null()->defaultExpression('NULL'),
                'width'             => $this->integer(11)->notNull()->defaultValue(0),
                'height'            => $this->integer(11)->notNull()->defaultValue(0),
                'length'            => $this->integer(11)->notNull()->defaultValue(0),
                'status'            => 'ENUM("active", "inactive", "deleted", "uploaded") NOT NULL',
                'createAt'          => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            ],
            $tableOptions
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%file}}');
    }
}
