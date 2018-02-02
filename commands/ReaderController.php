<?php
 
namespace app\commands;

use \Yii;
use \yii\console\Controller;
use app\models\User;
 
/**
 * Test controller
 */
class ReaderController extends Controller {
 
    public function actionIndex() {
        echo "cron service runnning";
    }
    
    public function actionOfflineByTimeout() {//yii reader/offline-by-timeout
        for($c = 0; $c < 10; $c++){
            User::OfflineByTimeout();
            sleep(rand(4, 5));
        }
    }
 
}