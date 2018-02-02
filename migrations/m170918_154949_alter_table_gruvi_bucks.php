<?php

use yii\db\Migration;

class m170918_154949_alter_table_gruvi_bucks extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%gruvi_bucks}}', 'paypalTransaction', 'VARCHAR(24) NULL');
		$this->createIndex('idx_paypalTransaction', '{{%gruvi_bucks}}', 'paypalTransaction', true);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%gruvi_bucks}}', 'paypalTransaction');
    }

}
