<?php

namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Response;
use \yii\filters\VerbFilter;
use \app\models\forms\LoginForm;
use \app\models\forms\ContactForm;
use \app\components\MainController;
use \app\models\User;
use \app\models\Site;
use \app\models\UserAuthType;
use \app\models\PasswordResetRequestForm;
use \app\models\ResetPasswordForm;
use \app\models\GruviBucks;

class SiteController extends MainController
{
    public $defaultAction = 'index';


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'only' => ['logout'],
//                'rules' => [
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
              'class' => 'yii\authclient\AuthAction',
              'successCallback' => [$this, 'oAuthSuccess'],
            ],
        ];
    }
    
    
    /**
    * This function will be triggered when user is successfuly authenticated using some oAuth client.
    *
    * @param yii\authclient\ClientInterface $client
    * @return boolean|yii\web\Response
    */
    public function oAuthSuccess($client) {
        
        // get user data from client
        $userAttributes = $client->getUserAttributes();
        if(!Yii::$app->user->isGuest)$this->goHome();
        
        if(empty($userAttributes['id'])){
            \Yii::$app->getSession()->setFlash('facebookError', "the facebook user not found");
            return $this->redirect('/site/login');
        }
        
        
        $current = UserAuthType::findOne(['socialNetworkId' => $userAttributes['id'], 'registrationType' => 'facebook']);
        
        if(!empty($current->userId)){
            $user = User::findIdentity($current->userId);
            Yii::$app->user->login($user, 3600*24);
            return $this->goHome();
        }else{
            $apiKey = md5(uniqid());
            $socInfo['firstName'] = (!empty($userAttributes['first_name']))?$userAttributes['first_name']:'';
            $socInfo['lastName'] = (!empty($userAttributes['first_name']))?$userAttributes['first_name']:'';
            $socInfo['socialId'] = $userAttributes['id'];
            $socInfo['email']    = (!empty($userAttributes['email']))?$userAttributes['email']:'';
            $socInfo['username'] = '';
            $socInfo['password'] = '';
            $socInfo['apiKey'] = $apiKey;
            if(!empty($userAttributes['birthday'])){
                $socInfo['dob'] = date("Y-m-d", strtotime($userAttributes['birthday']));
            }
                    
            $user = new User();
            $user->setScenario('facebookSignUp');
            $user->social = User::SOCIAL_FACEBOOK;
            $user->role = User::ROLE_USER;
            $user->email = $socInfo['email'];
            $user->firstName = $socInfo['firstName'];
            $user->lastName = $socInfo['lastName'];
            $user->apiKey = $socInfo['apiKey'];
            if(!empty($socInfo['dob']))$user->dob = $socInfo['dob'];
            if($user->validate() && $user->socialLogin(true, false, $socInfo)){
                GruviBucks::addBonusBucks($user->id);
                return $this->goHome();
            }
            
            $message = Site::get_error_summary($user->getErrors());
            \Yii::$app->getSession()->setFlash('facebookError', $message);
            return $this->redirect('/site/login');
        }
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if(User::isReader()){
           return $this->redirect(\yii\helpers\Url::to(['user/profile', 'id' => Yii::$app->user->getId()]));
        }
                
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionPrivacyPolicyes()
    {
        return $this->render('privacyPolicyes');
    }

    /**
     * @return string
     */
    public function actionTermsAndCondition()
    {
        return $this->render('termsAndCondition');
    }
    /**
     * @return string
     */
    public function actionPrivacyPolicy()
    {
        return $this->renderPartial('privacyPolicy');
    }

    /**
     * @return string
     */
    public function actionTermsAndService()
    {
        return $this->renderPartial('termsAndService');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            
			$user = new User();
			$user->email = $_REQUEST['LoginForm']['email'];
			$user->password = $_REQUEST['LoginForm']['password'];
			$user->social = User::SOCIAL_EMAIL;
            $user->rememberMe = (!empty($_REQUEST['LoginForm']['rememberMe'])?$_REQUEST['LoginForm']['rememberMe']:1);
			/*
			$socInfo = [];
            $socInfo['socialId'] = $user->email;
            $socInfo['email'] = $user->email;
            $socInfo['password'] = empty($this->password) ? '' : $this->hashPassword($this->password);
			*/
			$user->socialLogin(false/*, false, $socInfo*/);
			if(true === $user->hasErrors()) {

				//if(!empty($aErrors))Yii::$app->session->setFlash('error', implode(",", $aErrors));
				$model->addError('password', 'Access denied');

				return $this->render('login', [
					'model' => $model,
				]);
			}
			else {
                
                if(User::isReader()){
                    return $this->redirect(\yii\helpers\Url::to(['user/profile', 'id' => Yii::$app->user->getId()]));
                }elseif(User::isAdmin()){
                    return $this->redirect(\yii\helpers\Url::to(['user/readers']));
                }else{
                    return $this->redirect(Yii::$app->user->getReturnUrl());
                }
			}
			
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return Response
     */
//    public function actionLogout()
//    {
//        Yii::$app->user->logout();
//
//        return $this->goHome();
//    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
//    public function actionContact()
//    {
//        $model = new ContactForm();
//        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
//            Yii::$app->session->setFlash('contactFormSubmitted');
//
//            return $this->refresh();
//        }
//        return $this->render('contact', [
//            'model' => $model,
//        ]);
//    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout() {
        return $this->render('about');
    }

    // url: /site/notice-webrtc
    public function actionNoticeWebrtc() {
        return $this->render('notice_webrtc');
    }
    
   /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
