<?php

namespace app\controllers;

use \Yii;
use \app\components\MainController;
use \app\models\Site;

class ChatController extends MainController
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
    
   public function actionIndex(){
       
       return $this->render('index');
       
   }
   
}