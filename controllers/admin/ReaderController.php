<?php
namespace app\controllers\admin;

use \Yii;
use \app\components\MainController;
use \app\models\User;
use \app\models\Call;
//use \app\models\Message;
//use \app\models\UserCreditCard;
//use \app\models\Site;
//use \app\models\UserAuthType;
//use \app\models\File;
//use app\models\search\User as UserSearch;
//use app\components\widgets\ReadersTeaser;
//use \app\models\GruviBucks;
//use \app\models\UserRelation;       

class ReaderController extends MainController
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

    public function actionIndex()
    {
        $records = User::find()->where(['role' => 'reader'])
                               ->orderBy('createAt desc')
                               ->all();
        return $this->render('index', [ 'records' => $records ]);
    }

    public function actionShow($pkid)
    {
        $user = User::find()->where(['id' => $pkid])->one();
        $calls = Call::find()->where(['readerId' => $user->id])->all();
        return $this->render('show', [
            'u' => $user,
            'calls' => $calls,
        ]);
    }
    
    
}
