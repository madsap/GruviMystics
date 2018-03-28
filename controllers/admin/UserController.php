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
use \app\models\UserRelation;       

/**
 * Class UserController
 * @package app\controllers
 * @author  Alexander Mokhonko
 * Date: 06.07.17
 */
class UserController extends MainController
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
        $records = User::find()->where(['role' => 'user'])
                               ->orderBy('createAt desc')
                               ->all();
        return $this->render('index', [ 'records' => $records ]);
    }

    public function actionShow($pkid)
    {
        $user = User::find()->where(['id' => $pkid])->one();
        $calls = Call::find()->where(['customerId' => $user->id])->all();
        return $this->render('show', [
            'u' => $user,
            'calls' => $calls,
        ]);
    }

    
    public function actionIndexBlocked()
    {
        $blocked = UserRelation::find()
            ->where(['action' => 'block'])
            ->orderBy('createAt desc')
            ->all();
        //$searchModel = new UserSearch();
        //$readers = $searchModel->getReaders();
        
        //hh($blocked);
        return $this->render('blocked/index', [
            'blocked' => $blocked
        ]);
    }

    public function actionShowBlocked($id)
    {
        $blocked = UserRelation::find()
                    ->where(['id' => $id])
                    ->one();
        //$searchModel = new UserSearch();
        //$readers = $searchModel->getReaders();
        
        //hh($blocked);
        return $this->render('blocked/show', [
            'b' => $blocked
        ]);
    }

    /*
    public function actionSetInactive()
    {
        $so = Yii::$app->user->identity->UpdateActivity(User::ACTIVITY_DISABLED);
       // echo 'check!'.$so;exit;
        return Site::done_json();
    }
    
    public function actionAddReader()
    {
        
        if(!User::isAdmin()) return $this->goHome();
        $apiKey = md5(uniqid());
        $model = new User();
		$model->setScenario("addReader");
        
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            $post['User']['registrationType'] = User::SOCIAL_EMAIL;
            $post['User']['status'] = User::STATUS_ACTIVE;
            //$post['User']['role'] = User::ROLE_READER;
            $post['User']['apiKey'] = $apiKey;
            if($this->saveReader($model, $post, true)){ 
                return $this->redirect(['user/readers']);
            }
        }
        
        
        return $this->render('reader', [
            'model' => $model,
            'fileErrors' => $this->fileErrors,
            'is_action_add_reader' => true,
            'attributes' => []
        ]);
    }
    
    private function saveReader($model, $attributes, $isNewRecord = false){
        
        $aFiles = $this->_getFiles(User::FILENAME);
        $this->fileErrors = [];
        if(!empty($aFiles[User::FILENAME][0]['name'])){
            $file = new File();
            $validateFile = $file->saveImage(
                $model,
                User::FILENAME,
                $model->id,
                $model->id,
                User::MAIN_CATEGORY_LOGO,
                $aFiles
            );
            
            $this->fileErrors = $file->getErrors();
            if(empty($this->fileErrors[User::FILENAME])){
                $file->clearErrors();//conflict with main save
                $this->fileErrors = [];
                $validateFile  = true;
            }
        }else{
           $validateFile  = true;
        }

        $model->clearErrors();//due to $validateFile
        
        $loaded = $model->load($attributes);
        

        if ($loaded && true === $validateFile && $model->save()) {

            $model->social = USER::SOCIAL_EMAIL;
            $socialInfo = $model->getSocialInfo();
            if($isNewRecord){
                //yii doesn't work
                Yii::$app->getDb()->createCommand("UPDATE `md_user` SET `role` = :role WHERE `id` = ".$model->id, [':role' => User::ROLE_READER])->execute();
                UserAuthType::create($socialInfo, $model->id, $model->social, $socialInfo['socialId']);
            }else{
                if(!$model->updateAuthFields()){
                    UserAuthType::create($socialInfo, $model->id, $model->social, $socialInfo['socialId']);    
                }
            }
            
            
            if(isset($_REQUEST['User']['specialties']))$model->saveSpecialties();


            if(!empty($_REQUEST['User']['password']) || !empty($_REQUEST['User']['confirmPassword'])){

                $model->setScenario('setPassword');
                $model->password = $_REQUEST['User']['password'];
                $model->confirmPassword = $_REQUEST['User']['confirmPassword'];

                if($model->validate()) {
                    $model->setNewPassword($_REQUEST['User']['password']);
                }else{
                     return false;
                }
            }

            if(!empty($aFiles[User::FILENAME][0]['name'])){
                $file->saveImage(
                    $model,
                    User::FILENAME,
                    $model->id,
                    $model->id,
                    User::MAIN_CATEGORY_LOGO,
                    $aFiles,
                    'save'
                );
            }

            return true;

        }
       
        return false;
    }

    public function actionUpdate()
    {

        $model = Yii::$app->user->identity;
		$model->setScenario("update");
        
        if(User::isReader())return $this->redirect(['user/reader']);
        
        $post = Yii::$app->request->post();
        //just in case
        unset($post['registrationType']);unset($post['User']['registrationType']);
        unset($post['role']);unset($post['User']['role']);
        unset($post['status']);unset($post['User']['status']);
        
        if ($model->load($post) && $model->save()) {

            if(!$model->updateAuthFields()){
                $model->social = USER::SOCIAL_EMAIL;
                $socialInfo = $model->getSocialInfo();
                UserAuthType::create($socialInfo, $model->id, $model->social, $socialInfo['socialId']);    
            }

			if(!empty($_REQUEST['User']['password']) || !empty($_REQUEST['User']['confirmPassword'])){

				$model->setScenario('setPassword');
				$model->password = $_REQUEST['User']['password'];
				$model->confirmPassword = $_REQUEST['User']['confirmPassword'];

				if($model->validate()) {
					$model->setNewPassword($_REQUEST['User']['password']);
				}else{
					return $this->render('update', [
						'model' => $model
					]);
				}
			}

            return $this->redirect(['user/profile', 'id' => $model->id]);

        } 
            
        return $this->render('update', [
            'model' => $model
        ]);
            
    }
    
    
    public function actionBlockAjax(){

        if(!User::isAdmin() && !User::isReader()){
            return Site::done_json([], 'error', "503");
        }
            
        $model = new UserRelation();
        
        $request = Yii::$app->request->post();
        $model->senderId = Yii::$app->user->identity->id;
        $model->recipientId = !empty($request['userId'])?$request['userId']:null;
        $model->messageId = !empty($request['messageId'])?$request['messageId']:null;
        $model->action = UserRelation::ACTION_BLOCK;
        $model->setScenario("create");
        if ($model->create()) {
            Message::banByUser($model->senderId, $model->recipientId);
            return Site::done_json([]);
        } else {
            $message = Site::get_error_summary($model->getErrors());
            return Site::done_json([], 'error', $message);
        }
        
    }
    
    public function actionUnblockAjax(){

        $request = Yii::$app->request->post();
        if(empty($request['rowId']))return Site::done_json([], 'error', "no rowId");
            
        $model = (new UserRelation())->findOne(['id' => $request['rowId']]);
        if(empty($model))return Site::done_json([], 'error', "not found");
            
        if(!User::isAdmin() && $model->senderId != Yii::$app->user->identity->id){
            return Site::done_json([], 'error', "503");
        }
        
        Message::unbanByUser($model->senderId, $model->recipientId);
        
        if ($model->delete()) {
            return Site::done_json([]);
        } else {
            $message = Site::get_error_summary($model->getErrors());
            return Site::done_json([], 'error', $message);
        }
        
    }
    public function actionProfile($id = null)
    {
        if(empty($id))$id = Yii::$app->user->identity->id;
        
        $sql = "SELECT COUNT(*) as calls_count, SUM(`duration`) as calls_duration FROM `md_call` WHERE (`customerId` = :userId OR `readerId` = :userId) AND `status` = :status";
        $callsStatistic = Yii::$app->getDb()->createCommand($sql, [':userId' => $id, ':status' => Call::STATUS_DONE])->queryOne();
        
        $model = $this->findModel($id); // %PSG: model is profile being viewed
//hh('actionProfile:'.($model->id));
//hh('actionProfile:'.var_dump($model->id));
        
        if($model->getAttribute('role') != User::ROLE_READER){
            return $this->redirect(['/user/update']); // only readers' profiles are viewable
        }
        
        $isThisMyProfile =  ( !Yii::$app->user->isGuest && (Yii::$app->user->identity->id == $model->id) ); // %PSG: Note, false if not logged in
        if ( !$isThisMyProfile ) {
            // %PSG: if not viewing own profile (?)
            $this->view->params['readerAjaxUpdate'] = $model->id; 
        }
        
        return $this->render('profile', [
            'model' => $model,
            'chat' => $model->renderChat(),
            'specialties' => $model->getSpecialties(true),
            'callsStatistic' => $callsStatistic,
            //'editable' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $model->id)
            'editable' => $isThisMyProfile, // %PSG: must be logged in *and* viewing one's own profile
        ]);
    }
    
     */
    
}
