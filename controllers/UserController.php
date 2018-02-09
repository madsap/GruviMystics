<?php

namespace app\controllers;

use \Yii;
use \app\components\MainController;
use \app\models\User;
use \app\models\Call;
use \app\models\Message;
use \app\models\UserCreditCard;
use \app\models\Site;
use \app\models\UserAuthType;
use \app\models\File;
use app\models\search\User as UserSearch;
use app\components\widgets\ReadersTeaser;
use \app\models\GruviBucks;
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
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticate' => [
                'class'  => '\app\filters\AuthenticateFilter',
                'except' => ['sign-up', 'ping', 'login', 'check-online', 'forgot-password', 'reset-password', 'get-readers-teaser']
            ],
            'admin'        => [
                'class' => '\app\filters\AdminFilter',
                'only'  => ['list', 'add'],
            ],
            'guest'        => [
                'class' => '\app\filters\GuestFilter',
                'only'  => ['login', 'sign-up'],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {            
        if ($action->id == 'add-credit-card') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'login' page.
     * @return mixed
     */
    public function actionSignUp()
    {
        $apiKey = md5(uniqid());
        $user = new User();
        $user->setScenario('emailSignUp');
        $user->apiKey = $apiKey;
        $post = Yii::$app->request->post();
        $post['User']['apiKey'] = $apiKey;
        if(!isset($post['User']['dob']))$post['User']['dob'] = date('d-M-Y', strtotime('-20 years'));
        
        if (Yii::$app->request->post() && $user->load($post)){
            
            $user->social = User::SOCIAL_EMAIL;
            $user->role = User::ROLE_USER;
            //echo $user->load(Yii::$app->request->post()).' | '.$user->validate().' | '.$user->socialLogin();exit;
            if ($user->validate() && $user->socialLogin()) {
                GruviBucks::addBonusBucks($user->id);
                Yii::$app->session->setFlash('successRegistration', 'Success Registration. Sign In, Please!');
                
                return $this->redirect(['/user/login']);
            }
        }
        
        return $this->render('signUp', ['user' => $user]);
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        return $this->redirect(['/site/login']);
        /*
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);*/
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionProfile($id = null)
    {
        if(empty($id))$id = Yii::$app->user->identity->id;
        
        $sql = "SELECT COUNT(*) as calls_count, SUM(`duration`) as calls_duration FROM `md_call` WHERE (`customerId` = :userId OR `readerId` = :userId) AND `status` = :status";
        $callsStatistic = Yii::$app->getDb()->createCommand($sql, [':userId' => $id, ':status' => Call::STATUS_DONE])->queryOne();
        
        $model = $this->findModel($id);
        
        if($model->getAttribute('role') != User::ROLE_READER){
            return $this->redirect(['/user/update']);
        }
        
        if($model->id != Yii::$app->user->identity->id){
            $this->view->params['readerAjaxUpdate'] = $model->id;
        }
        
        return $this->render('profile', [
            'model' => $model,
            'chat' => $model->renderChat(),
            'specialties' => $model->getSpecialties(true),
            'callsStatistic' => $callsStatistic,
            'editable' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $model->id)
        ]);
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionSetActive()
    {
        //in this case active is non disabled
        $so = Yii::$app->user->identity->UpdateActivity(User::ACTIVITY_OFFLINE);
        return Site::done_json();
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionSetReaderOpt()
    {
        //in this case active is non disabled
        
        if(empty($_REQUEST['option'])){
            return Site::done_json([], "error", "incomplete request");
        }
        if($_REQUEST['option'] != "voice" && $_REQUEST['option'] != "chat" && $_REQUEST['option'] != "request"){
            return Site::done_json([], "error", "wrong option");
        }
        
        
        $option = $_REQUEST['option'];
        $status = (!empty($_REQUEST['status']) && $_REQUEST['status'] != "false")?1:0;
        
        $so = Yii::$app->user->identity->UpdateOpt($option, $status);
        return Site::done_json();
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionSetInactive()
    {
        $so = Yii::$app->user->identity->UpdateActivity(User::ACTIVITY_DISABLED);
       // echo 'check!'.$so;exit;
        return Site::done_json();
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionReader($id = null)
    {
        if(empty($id) || !User::isAdmin())$id = Yii::$app->user->identity->id;
        
        $model = User::findIdentity($id);
		$model->setScenario("update");
        
        //if(empty($model->id) || $model->getAttribute('role') != User::ROLE_READER) return $this->goHome();
        if(empty($model->id)) return $this->goHome();
        
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            //just in case
            if(!User::isAdmin()){
                unset($post['registrationType']);
                unset($post['role']);
                unset($post['status']);
            }elseif(User::isAdmin()){
                unset($post['registrationType']);
                unset($post['status']);
            }
            if($this->saveReader($model, $post)){
                return $this->redirect(['user/reader', 'id' => $model->id]);
            }
        }
        //echo "<pre>";
        //print_r($model);exit;
        return $this->render('reader', [
            'model' => $model,
            'fileErrors' => $this->fileErrors
        ]);
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionDeleteReader()
    {
        $id = Yii::$app->request->get('id');
        if(empty($id) || !User::isAdmin())return $this->goHome();
        
        $model = User::findIdentity($id);
        $model->setScenario("delete");
        
        $GruviBucks = GruviBucks::find()->where(['userId' => $id])->one(); 
        if(!empty($GruviBucks)) $GruviBucks->delete();
        
        $Call = Call::find()->where(['readerId' => $id])->one();    
        if(!empty($Call)) $Call->delete();
        
        $File = File::find()->where(['userId' => $id])->one(); 
        if(!empty($File)) $File->delete();
        
        $Message = Message::find()->where(['readerId' => $id])->one();   
        if(!empty($Message)) $Message->delete();
        
        $UserCreditCard = UserCreditCard::find()->where(['userId' => $id])->one(); 
        if(!empty($UserCreditCard)) $UserCreditCard->delete();
        
        $connection = Yii::$app->getDb();
        $connection->createCommand("DELETE FROM `md_user_specialty` WHERE `userId` = :user_id", [':user_id' => $id])->execute();
        
        $model->delete();     
        
        Yii::$app->session->setFlash('success', "Reader deleted successfully.");
        return $this->redirect(['user/readers']);
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
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


    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
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
    
    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionReaders()
    {
        $searchModel = new UserSearch();
        $readers = $searchModel->getReaders();
        
		//print_r($dataProvider);exit;
        return $this->render('readers', [
            'readers' => $readers
        ]);
    }
    
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

   
   public function actionGetReadersTeaser(){
       
       
       $filter = [];
       $page = (!empty($_REQUEST['page']))?$_REQUEST['page']:null;
       $recent_md5 = !(empty($_REQUEST['md5']))?$_REQUEST['md5']:null;
       if(!empty($_REQUEST['activity']))$filter['activity'] = $_REQUEST['activity'];
       if(!empty($_REQUEST['keyword']))$filter['keyword'] = $_REQUEST['keyword'];
       //print_r($_REQUEST);exit;
       $widget = ReadersTeaser::widget(['page' => $page, 'filter' => $filter, 'recent_md5' => $recent_md5]);
       
       return Site::done_json(['html' => $widget]);
   }
   
   public function actionAddCreditCard(){
       
       $html = "";
       if(empty($_REQUEST['token']))return Site::done_json('', 'error', 'token is empty');
       //stripe doesn't check duplicates
       //$card = (new UserCreditCard())->findOne(['token' => $_REQUEST['token'], 'userId' => Yii::$app->user->identity->id]);
       
       //if(empty($card->id)){
       
            try{
                \Stripe\Stripe::setApiKey(Yii::$app->params['stripe']['secretKey']);
                $stripe_customer = \Stripe\Customer::create(array(
                  "description" => "Customer for user #".Yii::$app->user->identity->id,
                  "source" => $_REQUEST['token'] // obtained with Stripe.js
                ));
            }catch (\Exception $e) {
                    return Site::done_json([], 'error', $e->getMessage());
                   // Yii::warning($e);
                }
       
            $card = new UserCreditCard();
            $card->userId = Yii::$app->user->identity->id;
            $card->token = $stripe_customer->id;
            if(!empty($_REQUEST['last4']))$card->last4 = $_REQUEST['last4'];
            if(!empty($_REQUEST['expiration']))$card->expiration = $_REQUEST['expiration'];
            if(!$card->save(false)){
                return Site::done_json([$card->getErrors(), 'error']);
            }
            $html = $this->renderPartial('@app/views/gruvi-bucks/credit-card-row', array('card'=>$card)); 
       //}
       
       return Site::done_json(['card' => $card, 'html' => $html]);
   }
   
    public function actionChargeDuringCall(){
        
        User::ChargeDuringCall();
        
    }
    
    public function actionGetAmountOfGruviBucks(){
        
        $amount = GruviBucks::getUserBalance(Yii::$app->user->identity->id);
        return Site::done_json(['amount' => $amount]);
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
    
    public function actionPing(){
        
        if(Yii::$app->user->isGuest)return Site::done_json([], 'error', 'login');
        
        
        $ret = [];
        
        $activeCall = Call::getActiveCall(Yii::$app->user->identity);
        
        $new_activity = (!empty($activeCall))?User::ACTIVITY_SESSION:User::ACTIVITY_ONLINE;
        Yii::$app->user->identity->UpdateActivity($new_activity);
        
        if(!User::isReader()){
            $ret['gruviBucks']['amount'] = GruviBucks::getUserBalance(Yii::$app->user->identity->id);
            if(!empty($activeCall) && $activeCall->status == Call::STATUS_CONVERSATION){
                if($activeCall->reader->rate > $ret['gruviBucks']['amount']){
                    $ret['gruviBucks']['toggleGruviBucksModal'] = "1";
                }elseif($activeCall->reader->rate*3 > $ret['gruviBucks']['amount']){
                    $ret['gruviBucks']['toggleGruviBucksModal'] = 'noAction';
                }else{
                    $ret['gruviBucks']['toggleGruviBucksModal'] = "0";
                }
                
            }
        }
        //$ret['call']['html'] = "test";
        //$ret['gruviBucks']['toggleGruviBucksModal'] = "1";
        
        if(!empty($activeCall)){
            $ret['call']['html'] = $activeCall->renderCallPopup(Yii::$app->user->identity->id);
        }
        
        $minMessageId = !empty($_REQUEST['minMessageId'])?$_REQUEST['minMessageId']:"0";
        if(!empty($_REQUEST['readerAjaxUpdate'])){
            $reader = User::findIdentity($_REQUEST['readerAjaxUpdate']);
            if(!empty($reader->id)){
                $ret['reader']['activity'] = $reader->activity;
                $ret['reader']['canCall'] = ($reader->canCall())?"1":"0";
                $ret['reader']['canChat'] = ($reader->canChat())?"1":"0";
                $ret['chat'] = $reader->renderChat($minMessageId);
            }
        }elseif(User::isReader()){
            $ret['chat'] = Yii::$app->user->identity->renderChat($minMessageId);
        }
        
        
        return Site::done_json($ret);
    }
    
}
