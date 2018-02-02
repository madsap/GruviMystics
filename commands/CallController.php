<?php
 
namespace app\commands;

use \Yii;
use \yii\console\Controller;
use app\models\User;

/**
 * Test controller
 */
class CallController extends Controller {
 
    public function actionIndex() {
        echo "cron service runnning";
    }
    
    public function actionChargeDuringCall() {//yii reader/offline-by-timeout
        for($c = 0; $c < 3; $c++){
            User::ChargeDuringCall();
            sleep(rand(10, 20));
        }
    }
 
}