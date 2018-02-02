<?php

use yii\db\Migration;

class m170818_131731_alter_table_gruvi_bucks extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%gruvi_bucks}}', 'callId', 'bigint(20) NULL');
        
		$this->addForeignKey(
			'fk_gruvi_bucks_CallId_to_call_id',
			'{{%gruvi_bucks}}',
			'callId',
			'{{%call}}',
			'id'
		);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%gruvi_bucks}}', 'callId');
    }
    
}
