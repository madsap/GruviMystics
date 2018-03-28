<?php

namespace app\controllers\admin;

use \Yii;
use \app\components\MainController;
use \app\models\User;
use \app\models\Call;

class HomeController extends MainController
{
    public $fileErrors = [];

    public function behaviors()
    {
        return [
            // Only allow admin access
            /* PSG: can't get this to work, use beforeAction() instead
            'authenticate' => [
                'class'  => '\app\filters\AuthenticateFilter',
                'only' => [],
            ],
            'guest'        => [
                'class' => '\app\filters\GuestFilter',
                'only' => [],
            ],
            'admin'        => [
                'class' => '\app\filters\AdminFilter',
                'except'  => [],
            ],
             */
        ];

    }
    
    public function beforeAction($action)
    {            
        if ( !User::isAdmin() ) {
            return $this->goHome();
        }
        return parent::beforeAction($action);
    }

    public function actionShow()
    {
        return $this->render('show');
    }

}
