<?php

namespace app\controllers;

use \Yii;
use \app\components\MainController;
use \app\models\Call;
use \app\models\Site;
use \app\models\User;

class CallController extends MainController
{
    //public $enableCsrfValidation = false;//Bad Request (#400): Unable to verify your data submission.
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticate' => [
                'class'  => '\app\filters\AuthenticateFilter',
                'except' => []
            ]
        ];
    }
    
   public function actionAnswer(){
       
       $activeCall = Call::getActiveCall(Yii::$app->user->identity);
       if(count($activeCall))$activeCall->answer();
       
       return Site::done_json(['html' => 'test']);
   }
    
   public function actionEnd(){
       
       $activeCall = Call::getActiveCall(Yii::$app->user->identity);
       if(count($activeCall))$activeCall->end();
       
       return Site::done_json(['html' => 'test']);
   }
   
   public function actionDetails(){
       
       $readerId = !(empty($_REQUEST['readerId']))?$_REQUEST['readerId']:null;
       
       $customer = null;
       $reader = null;
       $activeCall = Call::getActiveCall(Yii::$app->user->identity);
       
       
       if(empty($activeCall)){
           
           if(empty($readerId))return Site::done_json(['html' => 'no calls']);
           if($readerId == Yii::$app->user->identity->id)return Site::done_json(['html' => 'the line is busy']);
           $reader = User::findIdentity($readerId);
           $customer = Yii::$app->user->identity;
           
           $readerActiveCall = Call::getActiveCall($reader);
           if($reader->activity != User::ACTIVITY_ONLINE || count($readerActiveCall))return Site::done_json(['html' => 'the reader is not available / '.$reader->activity]);
           
           
           $activeCall = new Call();
           $activeCall->customerId = $customer->id;
           $activeCall->readerId = $reader->id;
           //we'll save when phone starts to ring
           //$activeCall->save(false);
           
       }else{
           //in case we need restore previous popup
           $reader = $activeCall->reader;
           $customer = $activeCall->customer;
           
       }
       
       $html = $activeCall->renderCallPopup(Yii::$app->user->identity->id, $customer, $reader);
       
       return Site::done_json(['html' => $html, 'show_popup' => 1, 'activeCall' => $activeCall]);
   }
   
}
