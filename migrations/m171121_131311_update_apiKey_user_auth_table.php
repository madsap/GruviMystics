<?php
use yii\db\Migration;

class m171121_131311_update_apiKey_user_auth_table extends Migration
{
    public function safeUp()
    {
        $rows = (new \yii\db\Query())
        ->select(['id','userId'])
        ->from('md_user_auth_type')->all();
        
        foreach($rows as $key => $row){
            $apiKey = md5(uniqid());
            $this->update('md_user_auth_type', ['apiKey' => $apiKey], ['id' => $row['id']]);
            $this->update('md_user',['apiKey' => $apiKey], ['id' => $row['userId']]);
        }
        
    }

    public function safeDown()
    {
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171121_131311_update_apiKey_user_auth_table cannot be reverted.\n";

        return false;
    }
    */
}
