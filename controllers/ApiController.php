<?php

namespace app\controllers;
use \Yii;
use \yii\db;
use \yii\filters\AccessControl;
use \yii\web\Response;
use \yii\filters\VerbFilter;
use \app\components\MainController;
use \app\models\User;
use \app\models\Call;
use \app\models\Message;
use \app\models\UserCreditCard;
use \app\models\Site;
use \app\models\UserAuthType;
use \app\models\File;
use app\models\search\User as UserSearch;
use app\models\search\UserRelation as UserRelationSearch;
use \app\models\User as UserModel;
use \app\models\GruviBucks;
use \app\models\UserRelation;
use \yii\helpers\Url;
use yii\helpers\Html;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Jwt\Grants\VoiceGrant;
use Twilio\Rest\Client;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

class ApiController extends MainController {

    public $defaultAction = 'index';
    public $fileErrors = [];
    protected $user;
    protected $call;
    protected $apiContext;
    protected $actual_link;
    protected $ReturnUrl;
    protected $CancelUrl;
    protected $SuccessUrl;
    protected $shipping;
    protected $quantity;
    protected $tax;
    protected $Currency;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, $config = array(), UserModel $user, Call $call) {
        parent::__construct($id, $module, $config);
        $this->user = $user;
        $this->call = $call;
        $this->actual_link = Url::to(["/"], true);
        $this->ReturnUrl = $this->actual_link . 'api/paypal-return';
        $this->CancelUrl = $this->actual_link . 'api/paypal-return?success=false';
        $this->SuccessUrl = $this->actual_link . 'api/paypal-return?success=true';
        $this->shipping = Yii::$app->params['paypal']['shipping'];
        $this->quantity = 1;
        $this->tax = Yii::$app->params['paypal']['tax'];
        $this->Currency = Yii::$app->params['paypal']['currency'];
        
    }

    public function behaviors() {
        return [
            'authenticate' => [
                'class' => '\app\filters\AuthenticateFilter',
                'only' => ['chat-token']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action) {
        //if ($action->id == 'fallback' || $action->id == 'voice' || $action->id == 'status') {
        $this->enableCsrfValidation = false;
        //}

        return parent::beforeAction($action);
    }

    public function actionIndex() {
        /* $leftJoin = [];
          $leftJoin[] = "LEFT JOIN `md_gruvi_bucks` gb ON gb.`callId` = c.`id` AND gb.`status` = :gruvi_bucks_status";
          $leftJoin[] = "LEFT JOIN `md_user` as r on r.`id` = c.`readerId`";

          $select = [];
          $select[] = "c.`id`";
          $select[] = "c.`twilioCallId`";
          $select[] = "c.`customerId`";
          $select[] = "c.`readerId`";
          $select[] = "r.`rate`";
          $select[] = "TIMESTAMPDIFF(SECOND, c.`callAnswerTime`,NOW()) as estimatedDuration";
          $select[] = "IF(SUM(gb.`amount`) IS NULL,0,SUM(gb.`amount`)) as paid";
          $select[] = "(CEIL(TIMESTAMPDIFF(SECOND, c.`callAnswerTime`,NOW()) / 60)*r.`rate`) as haveToPay";

          $sql = "SELECT " . implode(",", $select) . " FROM `md_call` as c " . implode(" ", $leftJoin) . " WHERE c.`status` = :call_status GROUP BY c.`id` HAVING (paid+haveToPay > 0)";
          //echo $sql;exit;
          $connection = Yii::$app->getDb();
          echo $command = $connection->createCommand($sql, ['call_status' => Call::STATUS_CONVERSATION, 'gruvi_bucks_status' => GruviBucks::STATUS_APPROVED])->getRawSql();
          exit;
          $activeCalls = $command->queryAll();
          echo $connection->createCommand()->getRawSql(); */

         /*$rows = (new \yii\db\Query())
          ->select(['id','userId'])
          ->from('md_user_auth_type')->where(['apiKey' => ""])->all();

          foreach($rows as $key => $row){
          $apiKey = md5(uniqid());
          $connection = Yii::$app->getDb();
          $command = $connection->createCommand("update md_user_auth_type set apiKey = '".$apiKey."' where id = '".$row['id']."'");
          $command->execute();


          $command1 = $connection->createCommand("update md_user set apiKey = '".$apiKey."' where id = '".$row['userId']."'");
          $command1->execute();

          } 
        */
        $response['error'] = true;
        $response['msg'] = "No direct access allowed.";
        echo json_encode($response);
        exit;
    }

    public function actionPaypalpayment() {
        $header = array();
        $header_fields = array();
        $post = Yii::$app->request->get();
        $request_fields = array('amount', 'apiKey');
        $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);

        if (!$request_form_success) {
            return $this->redirect($this->CancelUrl, 302)->send();
        } else {
            $apiKey = $post['apiKey'];
            $validateLogin = $this->checkLogin($apiKey);
            if ($validateLogin) {
                try {
                    $shipping = $this->shipping;
                    $tax = $this->tax;
                    $quantity = $this->quantity;
                    $Currency = $this->Currency;
                    $description = 'Paypal Charge for userId : ' . $validateLogin->id;
                    $price = $post['amount'];
                    $total = $price + $shipping;

                    $this->apiContext = new ApiContext(new OAuthTokenCredential(Yii::$app->params['paypal']['production']['ClientID'], Yii::$app->params['paypal']['production']['secret']));

                    $payer = new Payer();
                    $payer->setPaymentMethod("PayPal");

                    $item1 = new Item();
                    $item1->setName($description)
                            ->setDescription($description)
                            ->setCurrency($Currency)
                            ->setQuantity($quantity)
                            ->setTax($tax)
                            ->setPrice($price);

                    $itemList = new ItemList();
                    $itemList->setItems(array($item1));


                    $details = new Details();
                    $details->setShipping($shipping)
                            ->setTax($tax)
                            ->setSubtotal($price);

                    $amount = new Amount();
                    $amount->setCurrency($Currency)
                            ->setTotal($total)
                            ->setDetails($details);

                    $transaction = new Transaction();
                    $transaction->setAmount($amount)
                            ->setItemList($itemList)
                            ->setDescription("Payment")
                            ->setInvoiceNumber(uniqid());

                    $redirectUrls = new RedirectUrls();
                    $redirectUrls->setReturnUrl($this->ReturnUrl)
                            ->setCancelUrl($this->CancelUrl);


                    $payment = new Payment();
                    $payment->setIntent("sale")
                            ->setPayer($payer)
                            ->setRedirectUrls($redirectUrls)
                            ->setTransactions(array($transaction));


                    $payment->create($this->apiContext);

                    $paymentId = $payment->getId();

                    $approvalUrl = $payment->getApprovalLink();


                    $model = new GruviBucks();
                    $model->userId = $validateLogin->id;
                    $model->amount = $price;
                    $model->log = "Paypal payment";
                    $model->paypalTransaction = $paymentId;
                    $model->status = GruviBucks::STATUS_DECLINED;
                    $model->addGruviBucks();


                    return $this->redirect($approvalUrl, 302)->send();
                } catch (\Exception $exception) {
                    return $this->redirect($this->CancelUrl, 302)->send();
                }
            } else {
                return $this->redirect($this->CancelUrl, 302)->send();
            }
        }
    }

    public function actionPaypalReturn() {
        $post = Yii::$app->request->get();
        $transcation = (isset($post['success'])) ? $post['success'] : "";
        if (isset($post['PayerID']) && isset($post['paymentId'])) {
            $transaction_id = $post['paymentId'];
            $customer_id = $post['PayerID'];
            $execution = new PaymentExecution();
            $execution->setPayerId($customer_id);

            try {
                $this->apiContext = new ApiContext(new OAuthTokenCredential(Yii::$app->params['paypal']['production']['ClientID'], Yii::$app->params['paypal']['production']['secret']));
                $payment = Payment::get($transaction_id, $this->apiContext);
                $result = $payment->execute($execution, $this->apiContext);

                $model = GruviBucks::find()->where(['paypalTransaction' => $transaction_id])->one();

                $model->status = GruviBucks::STATUS_APPROVED;
                $model->save();

                return $this->redirect($this->SuccessUrl, 302)->send();
            } catch (\Exception $ex) {
                return $this->redirect($this->CancelUrl, 302)->send();
            }
        }
    }

    public function actionLogin() {
        $post = Yii::$app->request->post();
        $response = array();
        $request_fields = array('email', 'password');

        $request_form_success = $this->verifyPost(array(), array(), $post, $request_fields);

        if (!$request_form_success) {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        } else {

            $user = new User();
            $user->email = $post['email'];
            $user->password = $post['password'];
            $user->social = User::SOCIAL_EMAIL;

            $result = $user->socialLogin(false, false, [], TRUE);

            if (true === $user->hasErrors()) {
                $response['error'] = true;
                $response['msg'] = 'Password is incorrect.';
            } else {
                $user->id = $result['id'];
                if (!empty($result)) {
                    $response['error'] = false;
                    $response['msg'] = 'Success';
                    $response['result'] = $result;
                    $response['result']['displayname'] = (isset($result['displayname']) && $result['displayname'] != NULL) ? $result['displayname'] : "";
                    $response['creditCardCount'] = $user->getCreditCardCount();
                    $response['result']['pic'] = $user->getProfilePicUrl();
                    $response['bucks'] = $user->getGruviBucksAmount();
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Email or Password is incorrect.';
                }
            }
        }
        echo json_encode($response);
    }

    public function actionSingup() {

        $response = array();
        $post = Yii::$app->request->post();
        $request_fields = array('method');
        $request_form_success = $this->verifyPost(array(), array(), $post, $request_fields);

        if (!$request_form_success) {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        } else {
            $method = $post['method'];
            if ($method == 1) {
                $response = $this->normalLogin($post);
            } elseif ($method == 2) {
                $response = $this->facebookLogin($post);
            } elseif ($method == 3) {
                $response = $this->twitterLogin($post);
            } else {
                $response['error'] = true;
                $response['msg'] = 'Invalid Request , please try again.';
            }
        }
        echo json_encode($response);
    }

    protected function normalLogin($post) {
        $response = array();
        $request_fields = array('firstName', 'lastName', 'email', 'password', 'confirmPassword');
        $request_form_success = $this->verifyPost(array(), array(), $post, $request_fields);
        if (!$request_form_success) {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        } else {
            $apiKey = md5(uniqid());
            $request['User'] = $post;
            $request['User']['apiKey'] = $apiKey;
            $request['User']['telephone'] = (isset($post['telephone']) && !empty($post['telephone'])) ? $post['telephone'] : '';
            $user = new User();
            $user->setScenario('emailSignUp');
            $user->apiKey = $apiKey;
            $user->telephone = (isset($post['telephone']) && !empty($post['telephone'])) ? $post['telephone'] : '';
            if (!isset($post['dob'])) {
                $request['User']['dob'] = date('Y-m-d', strtotime('-20 years'));
            } elseif (!empty($post['dob'])) {
                $request['User']['dob'] = date("Y-m-d", strtotime($post['dob']));
            } else {
                $request['User']['dob'] = date('Y-m-d', strtotime('-20 years'));
            }

            if (Yii::$app->request->post() && $user->load($request)) {
                $user->social = User::SOCIAL_EMAIL;
                $user->role = User::ROLE_USER;

                $checkUserResult = $user->findSocialId($post['email'], null);

                if (!empty($checkUserResult)) {
                    $response['error'] = true;
                    $response['msg'] = $post['email'] . ' has already been taken.';
                } else {
                    $UserResult = $user->socialLogin(true, false, [], TRUE, 'Singup');
                    if ($UserResult == true) {

                        $saveBase64Image = (isset($post['photo']) && $post['photo'] != "") ? $post['photo'] : "";
                        $file = new File();
                        $validateFile = $file->saveBase64Image(
                                $saveBase64Image, $user->id, User::MAIN_CATEGORY_LOGO
                        );
                        GruviBucks::addBonusBucks($user->id);
                        $response['error'] = false;
                        $response['msg'] = 'Success Registration. Sign In, Please!';
                    } else {
                        $response['error'] = true;
                        $response['msg'] = 'Failed to registering user.';
                    }
                }
            } else {
                $response['error'] = true;
                $response['msg'] = 'Invalid request.';
            }
        }
        return $response;
    }

    protected function facebookLogin($post) {
        $response = array();
        $request_fields = array('firstName', 'lastName', 'email', 'socialId', 'password', 'confirmPassword');

        $request_form_success = $this->verifyPost(array(), array(), $post, $request_fields);
        if (!$request_form_success) {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        } else {

            $current = UserAuthType::find()->where('email = :email and registrationType != :registrationType', ['email' => $post['email'], 'registrationType' => User::SOCIAL_FACEBOOK])->one();
            if (isset($current->userId) && !empty($current->userId)) {
                $response['error'] = true;
                $response['msg'] = 'Email address already exsist.';
            } else {
                $user = new User();
                $user->setScenario('facebookSignUp');
                $user->social = User::SOCIAL_FACEBOOK;
                $user->role = User::ROLE_USER;
                $facebookUserResult = $user->findSocialId($post['socialId'], null);
                if (!empty($facebookUserResult)) {
                    $response['error'] = false;
                    $response['msg'] = 'Success.';
                    $response['creditCardCount'] = $facebookUserResult->getCreditCardCount();
                    $response['result'] = $facebookUserResult->getAttributes();
                    $response['result']['displayname'] = (isset($facebookUserResult->displayname) && $facebookUserResult->displayname != NULL) ? $facebookUserResult->displayname : "";
                    $response['result']['pic'] = $facebookUserResult->getProfilePicUrl();
                    $response['bucks'] = $facebookUserResult->getGruviBucksAmount();
                } else {
                    $apiKey = md5(uniqid());
                    $socInfo['firstName'] = (!empty($post['firstName'])) ? $post['firstName'] : '';
                    $socInfo['lastName'] = (!empty($post['lastName'])) ? $post['lastName'] : '';
                    $socInfo['socialId'] = $post['socialId'];
                    $socInfo['email'] = (!empty($post['email'])) ? $post['email'] : '';
                    $socInfo['username'] = $post['socialId'];
                    $socInfo['telephone'] = (isset($post['telephone']) && !empty($post['telephone'])) ? $post['telephone'] : '';
                    $socInfo['password'] = '';
                    $socInfo['apiKey'] = $apiKey;
                    if (!isset($post['dob'])) {
                        $socInfo['dob'] = date('Y-m-d', strtotime('-20 years'));
                    } elseif (!empty($post['dob'])) {
                        $socInfo['dob'] = date("Y-m-d", strtotime($post['dob']));
                    } else {
                        $socInfo['dob'] = date('Y-m-d', strtotime('-20 years'));
                    }
                    $user->email = $socInfo['email'];
                    $user->firstName = $socInfo['firstName'];
                    $user->lastName = $socInfo['lastName'];
                    $user->apiKey = $apiKey;
                    $user->telephone = $socInfo['telephone'];
                    if (!empty($socInfo['dob'])) {
                        $user->dob = $socInfo['dob'];
                    }
                    $checkUserResult = $user->socialLogin(true, false, $socInfo, true);
                    if (!empty($checkUserResult)) {
                        $saveBase64Image = (isset($post['photo']) && $post['photo'] != "") ? $post['photo'] : "";
                        $file = new File();
                        $validateFile = $file->saveBase64Image(
                                $saveBase64Image, $user->id, User::MAIN_CATEGORY_LOGO
                        );
                        GruviBucks::addBonusBucks($user->id);
                        $response['error'] = false;
                        $response['msg'] = 'Success.';
                        $response['creditCardCount'] = $checkUserResult->getCreditCardCount();
                        $response['result'] = $checkUserResult->getAttributes();
                        $response['result']['displayname'] = (isset($checkUserResult->displayname) && $checkUserResult->displayname != NULL) ? $checkUserResult->displayname : "";
                        $response['result']['pic'] = $checkUserResult->getProfilePicUrl();
                        $response['bucks'] = $checkUserResult->getGruviBucksAmount();
                    } else {
                        $response['error'] = true;
                        $response['msg'] = 'Failed to regsitering User.';
                    }
                }
            }
        }
        return $response;
    }

    protected function twitterLogin($post) {
        $response = array();
        $request_fields = array('firstName', 'lastName', 'email', 'socialId', 'password', 'confirmPassword');

        $request_form_success = $this->verifyPost(array(), array(), $post, $request_fields);
        if (!$request_form_success) {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        } else {
            $user = new User();
            $user->setScenario('twitterSignUp');
            $user->social = User::SOCIAL_TWITTER;
            $user->role = User::ROLE_USER;
            $twitterUserResult = $user->findSocialId($post['socialId'], null);
            if (!empty($twitterUserResult)) {
                $response['error'] = false;
                $response['msg'] = 'Success.';
                $response['creditCardCount'] = $twitterUserResult->getCreditCardCount();
                $response['result'] = $twitterUserResult->getAttributes();
                $response['result']['displayname'] = (isset($twitterUserResult->displayname) && $twitterUserResult->displayname != NULL) ? $twitterUserResult->displayname : "";
                $response['result']['pic'] = $twitterUserResult->getProfilePicUrl();
                $response['bucks'] = $twitterUserResult->getGruviBucksAmount();
            } else {
                $apiKey = md5(uniqid());
                $socInfo['firstName'] = (!empty($post['firstName'])) ? $post['firstName'] : '';
                $socInfo['lastName'] = (!empty($post['lastName'])) ? $post['lastName'] : '';
                $socInfo['socialId'] = $post['socialId'];
                $socInfo['email'] = (!empty($post['email'])) ? $post['email'] . "@gruvimystics.com" : '';
                $socInfo['username'] = $post['socialId'];
                $socInfo['telephone'] = (isset($post['telephone']) && !empty($post['telephone'])) ? $post['telephone'] : '';
                $socInfo['password'] = '';
                $socInfo['apiKey'] = $apiKey;
                if (!isset($post['dob'])) {
                    $socInfo['dob'] = date('Y-m-d', strtotime('-20 years'));
                } elseif (!empty($post['dob'])) {
                    $socInfo['dob'] = date("Y-m-d", strtotime($post['dob']));
                } else {
                    $socInfo['dob'] = date('Y-m-d', strtotime('-20 years'));
                }
                $user->email = $socInfo['email'];
                $user->firstName = $socInfo['firstName'];
                $user->lastName = $socInfo['lastName'];
                $user->apiKey = $apiKey;
                $user->telephone = $socInfo['telephone'];
                if (!empty($socInfo['dob'])) {
                    $user->dob = $socInfo['dob'];
                }
                $checkUserResult = $user->socialLogin(true, false, $socInfo, true);
                if (!empty($checkUserResult)) {
                    $saveBase64Image = (isset($post['photo']) && $post['photo'] != "") ? $post['photo'] : "";
                    $file = new File();
                    $validateFile = $file->saveBase64Image(
                            $saveBase64Image, $user->id, User::MAIN_CATEGORY_LOGO
                    );
                    GruviBucks::addBonusBucks($user->id);
                    $response['error'] = false;
                    $response['msg'] = 'Success.';
                    $response['creditCardCount'] = $checkUserResult->getCreditCardCount();
                    $response['result'] = $checkUserResult->getAttributes();
                    $response['result']['displayname'] = (isset($checkUserResult->displayname) && $checkUserResult->displayname != NULL) ? $checkUserResult->displayname : "";
                    $response['result']['pic'] = $checkUserResult->getProfilePicUrl();
                    $response['bucks'] = $checkUserResult->getGruviBucksAmount();
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Failed to regsitering User.';
                }
            }
        }
        return $response;
    }

    public function actionDashboard() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();

        if (!empty($header)) {
            $request_fields = array('apiKey');

            $request_form_success = $this->verifyPost($header, $request_fields, array(), array());

            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
//                    $activeCall = Call::getActiveCall($validateLogin);
//                    $new_activity = (!empty($activeCall)) ? User::ACTIVITY_SESSION : User::ACTIVITY_ONLINE;
//                    $validateLogin->UpdateActivity($new_activity);

                    $validateLogin->opt_voice = 1;
                    $validateLogin->isMobile = 1;
                    //$validateLogin->activity = User::ACTIVITY_ONLINE;
                    //$validateLogin->activity_update = date('Y-m-d H:i:s');
                    $validateLogin->save();
                    $currentUserData = $validateLogin->getAttributes();
                    $userType = $currentUserData['role'];
                    if ($userType == User::ROLE_USER) {
                        $query = UserModel::find()->alias('u')->select('u.*');
                        $query->where('u.role = :role', [':role' => User::ROLE_READER]);

                        $results = $query->all();
                        $readers = array();
                        $i = 0;
                        foreach ($results as $key => $reader) {
                            if($validateLogin->amIBlockingThisUser($reader->id)){
                                continue;
                            }
                            $speciality = $reader->getSpecialties(true);
                            $recent = Call::find()->select(['customerId'])->where(['readerId' => $reader->id, 'customerId' => $validateLogin->id])->groupBy(['customerId'])->count();
                            $readers[$i] = $reader->getAttributes();
                            $readers[$i]['displayname'] = (isset($reader->displayname) && $reader->displayname != NULL) ? $reader->displayname : "";
                            $readers[$i]['speciality'] = $speciality;
                            $readers[$i]['recent'] = ($recent > 0) ? TRUE : FALSE;
                            $readers[$i]['pic'] = $reader->getProfilePicUrl();
                            $readers[$i]['pic'] = $reader->getProfilePicUrl();
                            $i++;
                        }
                        $response['readers'] = $readers;
                    } else {
                        $messageShow = array();
                        $callsCount = 0;
                        $callDuration = 0;
                        $year = date('Y');
                        $month = date('m');
                        $maxMessageId = (isset($post['maxMessageId'])) ? $post['maxMessageId'] : "";
                        $minMessageId = (isset($post['minMessageId'])) ? $post['minMessageId'] : "";
                        $messages = $validateLogin->getChat($minMessageId, 0, $maxMessageId);
                        $callsStatistic = $this->call->getcallsStatistic($currentUserData['id'], $month, $year);
                        $speciality = $validateLogin->getSpecialties(true);
                        if (!empty($callsStatistic)) {
                            $callsCount = $callsStatistic['calls_count'];
                            $callDuration = ($callsStatistic['calls_duration'] > 0) ? round($callsStatistic['calls_duration'] / 60, 0) : 0;
                            $year = $callsStatistic['year'];
                            $month = $callsStatistic['month'];
                        }
                        if (!empty($messages)) {
                            foreach ($messages as $key => $message) {
                                $createAt = $this->convert_time_zone($message->createAt,'UTC','America/Los_Angeles');
                                $messageShow[$key] = $message->getAttributes();
                                $messageShow[$key]['time'] = date('h:i A', strtotime($createAt));
                                $messageShow[$key]['firstName'] = $message->customer->firstName;
                                $messageShow[$key]['lastName'] = $message->customer->lastName;
                            }
                        }
                        $response['chat'] = $messageShow;
                        $response['callsCount'] = $callsCount;
                        $response['callsDuration'] = (String) $callDuration;
                        $response['month'] = $month;
                        $response['year'] = $year;
                        $response['speciality'] = $speciality;
                    }
                    $response['error'] = FALSE;
                    $response['creditCardCount'] = $validateLogin->getCreditCardCount();
                    $response['bucks'] = $validateLogin->getGruviBucksAmount();
                    $response['profile'] = $currentUserData;
                    $response['profile']['displayname'] = (isset($currentUserData['displayname']) && $currentUserData['displayname'] != NULL) ? $currentUserData['displayname'] : "";
                    $response['profile']['pic'] = $validateLogin->getProfilePicUrl();
                    $response['msg'] = 'Success.';
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required header missing.';
        }
        echo json_encode($response);
    }

    public function actionGetreader() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();

        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('readerId');

            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);

            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $readerId = $post['readerId'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $readerResult = $this->user->findByUserId($readerId);
                    if (!empty($readerResult)) {
                        $speciality = $readerResult->getSpecialties(true);
                        $profile = $readerResult->getAttributes();
                        $clientName = $readerResult->getNameForTwilio(); //$post['clientName'];
                        $messageShow = array();
                        $lastMessageId = (isset($post['lastMessageId'])) ? $post['lastMessageId'] : 0;
                        $messages = $readerResult->getChat(0, 0, $lastMessageId);
                        if (!empty($messages)) {
                            foreach ($messages as $key => $message) {
                                $createAt = $this->convert_time_zone($message->createAt,'UTC','America/Los_Angeles');
                                $messageShow[$key] = $message->getAttributes();
                                $messageShow[$key]['time'] = date('h:i A', strtotime($createAt));
                                $messageShow[$key]['firstName'] = $message->customer->firstName;
                                $messageShow[$key]['lastName'] = $message->customer->lastName;
                            }
                        }
                        $response['error'] = false;
                        $response['msg'] = 'Success.';
                        $response['profile'] = $profile;
                        $response['profile']['displayname'] = (isset($profile['displayname']) && $profile['displayname'] != NULL) ? $profile['displayname'] : "";
                        $response['profile']['pic'] = $readerResult->getProfilePicUrl();
                        $response['profile']['identity'] = $clientName;
                        $response['speciality'] = $speciality;
                        $response['chat'] = $messageShow;
                    } else {
                        $response['error'] = true;
                        $response['msg'] = 'Reader not found.';
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionClienttoken() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array();
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    $id = $currentUserData['id'];
                    $clientName = $validateLogin->getNameForTwilio(); //$post['clientName'];
                    $device = ((isset($post['device'])) && ($post['device']) == 1) ? 1 : 0;
                    if ($device == 1) {
                        $pushcredentials = Yii::$app->params['twilio']['androidToken'];   //android
                    } else {
                        $pushcredentials = Yii::$app->params['twilio']['iosToken'];    //ios
                    }

                    try {
                        $token = new AccessToken(
                                Yii::$app->params['twilio']['accountSid'], Yii::$app->params['twilio']['apiKey'], Yii::$app->params['twilio']['apiKeySecret'], 3600, $clientName
                        );

                        $voiceGrant = new VoiceGrant();
                        $voiceGrant->setOutgoingApplicationSid(Yii::$app->params['twilio']['appSid']);
                        $voiceGrant->setPushCredentialSid($pushcredentials);

                        header('Content-type:application/json;charset=utf-8');

                        $token->addGrant($voiceGrant);
                        $access_token = $token->toJWT();

                        $response['error'] = false;
                        $response['msg'] = 'Token Created successfully.';
                        $response['identity'] = $clientName;
                        $response['token'] = $access_token;
                    } catch (\Exception $e) {
                        $response['error'] = true;
                        $response['msg'] = $e->getMessage();
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionEndcall() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array();
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $activeCall = Call::getActiveCall($validateLogin);
                    if (count($activeCall)) {
                        $activeCall->end();
                        $customerId = $activeCall->customerId;
                        $UserModel = User::findIdentity($customerId);
                        $response['bucks'] = $UserModel->getGruviBucksAmount();
                    }
                    $response['error'] = false;
                    $response['msg'] = 'Success.';
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionAnswercall() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array();
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $activeCall = Call::getActiveCall($validateLogin);
                    if (count($activeCall))
                        $activeCall->answer();
                    $response['error'] = false;
                    $response['msg'] = 'Success.';
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionAddbucks() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array();
            if (isset($post['cardId'])) {
                $request_fields = array('cardId', 'amount');
            } else {
                $request_fields = array('tokenId', 'amount');
            }
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    try {
                        $currentUserData = $validateLogin->getAttributes();
                        $customerID = '';
                        $creditCardId = '';
                        $amount = (float) $post['amount'] * 100;
                        $model = new GruviBucks();
                        \Stripe\Stripe::setApiKey(Yii::$app->params['stripe']['secretKey']);
                        if (isset($post['cardId'])) {
                            $cardId = $post['cardId'];
                            $card = (new UserCreditCard())->findOne(['userId' => $currentUserData['id'], 'id' => $cardId]);
                            if (empty($card)) {
                                $response['error'] = true;
                                $response['msg'] = 'CreditCard not found.';
                                echo json_encode($response);
                                die();
                            }
                            $creditCardId = $cardId;
                            $customerID = $card->token;
                        } else {
                            $token = $post['tokenId'];

                            //Creating new customer in Stripe.
                            $customer = \Stripe\Customer::create(array(
                                        "email" => $currentUserData['email'],
                                        "description" => "Customer for user #" . $currentUserData['id'],
                                        "source" => $token // obtained from the Token Object
                            ));

                            //Retrive Customer ID from the customer object.

                            $customerID = $customer->id;

                            $stripeCardId = $customer->default_source;

                            $cardDetails = $customer->sources->retrieve($stripeCardId);

                            //$customer->sources->retrieve("card_1BTsTtAUsVPptQnbpPxU8YxJ");

                            $UserCreditCard = new UserCreditCard();
                            $UserCreditCard->userId = $currentUserData['id'];
                            $UserCreditCard->token = $customerID;
                            $UserCreditCard->last4 = $cardDetails->last4;
                            $UserCreditCard->expiration = $cardDetails->exp_month . "/" . $cardDetails->exp_year;
                            if (!$UserCreditCard->save(false)) {
                                $response['error'] = true;
                                $response['msg'] = $UserCreditCard->getErrors();
                                echo json_encode($response);
                                die();
                            }
                            $creditCardId = $UserCreditCard->id;
                        }
                        $chargeDetails = \Stripe\Charge::create(array(
                                    "amount" => $amount,
                                    "currency" => "usd",
                                    "description" => "Charge for customer #" . $currentUserData['id'] . " " . $currentUserData['email'],
                                    "customer" => $customerID
                        ));

                        $model->userId = $currentUserData['id'];
                        $model->creditCardId = $creditCardId;
                        $model->stripeTransaction = $chargeDetails->id;
                        $model->amount = $chargeDetails->amount / 100;
                        $model->log = "";
                        $model->addGruviBucks();

                        $creditCardsResult = $validateLogin->getCreditCards();
                        $creditCards = array();
                        foreach ($creditCardsResult as $key => $card) {
                            $creditCards[$key] = $card->getAttributes();
                        }

                        $response['error'] = false;
                        $response['msg'] = 'Successfully added bucks.';
                        $response['creditcard'] = $creditCards;
                        $response['bucks'] = $validateLogin->getGruviBucksAmount();
                    } catch (\Exception $exception) {
                        $response['error'] = true;
                        $response['msg'] = 'Payment failed. ' . $exception->getMessage();
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionGetbucks() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array();
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $response['error'] = false;
                    $response['msg'] = 'Success';
                    $response['bucks'] = $validateLogin->getGruviBucksAmount();
                    //$response['activity'] = $validateLogin->activity;
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionSetstatus() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('status');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    $id = $currentUserData['id'];
                    $userType = $currentUserData['role'];
                    $status = ($post['status'] == 'offline') ? User::ACTIVITY_OFFLINE : User::ACTIVITY_ONLINE;
                    if ($userType == User::ROLE_READER) {
                        $update = $validateLogin->UpdateActivity($status);
                        if ($update) {
                            $response['error'] = false;
                            $response['msg'] = "Status updated successfully.";
                        } else {
                            $response['error'] = true;
                            $response['msg'] = "Failed to update status.";
                        }
                    } else {
                        $response['error'] = true;
                        $response['msg'] = "You don't have permission to set status.";
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionChangerate() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('rate');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    $userType = $currentUserData['role'];
                    $rate = (float) $post['rate'];
                    if ($userType == User::ROLE_READER) {
                        $update = $validateLogin->UpdateRate($rate);
                        if ($update) {
                            $response['error'] = false;
                            $response['msg'] = "Rate updated successfully.";
                            $response['rate'] = $validateLogin->rate;
                        } else {
                            $response['error'] = true;
                            $response['msg'] = "Failed to update rate.";
                        }
                    } else {
                        $response['error'] = true;
                        $response['msg'] = "You don't have permission to change rate.";
                    }
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionChangepassword() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('oldPassword', 'newPassword');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    $oldPassword = $validateLogin->hashPassword($post['oldPassword']);
                    if ($oldPassword == $validateLogin->authType[0]->password) {
                        $validateLogin->setScenario('setPassword');
                        $validateLogin->password = $post['newPassword'];
                        $validateLogin->confirmPassword = $post['newPassword'];
                        if ($validateLogin->validate()) {
                            $validateLogin->setNewPassword($post['newPassword']);
                            $response['error'] = false;
                            $response['msg'] = "Password updated successfully.";
                        } else {
                            $response['error'] = false;
                            $response['msg'] = "Failed to update password.";
                        }
                    } else {
                        $response['error'] = true;
                        $response['msg'] = "Old password do not match.";
                    }
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionGetusers() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array();
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    $id = $currentUserData['id'];
                    $userType = $currentUserData['role'];
                    if ($userType == User::ROLE_READER) {
                        $query = UserModel::find()->alias('u')->select('u.*');
                        $query->where('u.role = :role', [':role' => User::ROLE_USER]);
                        $results = $query->all();

                        $queryBlock = UserRelation::find();
                        $queryBlock->where('senderId = :senderId', [':senderId' => $id]);
                        $resultsBlock = $queryBlock->all();
                        $users = $blockusers = $blockedUser = $unblockedUser = array();
                        foreach ($resultsBlock as $bkey => $buser) {
                            $blockusers[$bkey] = $buser->getAttributes();
                        }

                        foreach ($results as $key => $user) {
                            $users[$key] = $user->getAttributes();
                            if ($this->searcharray($user->id, 'recipientId', $blockusers)) {
                                $blockedUser[] = $users[$key];
                            } else {
                                $unblockedUser[] = $users[$key];
                            }
                        }
                        $response['error'] = FALSE;
                        $response['msg'] = 'success';
                        //$response['users'] = $users;
                        $response['blockedUser'] = $blockedUser;
                        $response['unblockedUser'] = $unblockedUser;
                    } else {
                        $response['error'] = true;
                        $response['msg'] = "You don't have permission to get users.";
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Invalid reader, please try again.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionUpdateuser() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('firstName', 'lastName', 'email', 'telephone');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    //$currentUserData = $validateLogin->getAttributes();
                    if ($validateLogin->role == User::ROLE_USER) {

                        $saveBase64Image = (isset($post['photo']) && $post['photo'] != "") ? $post['photo'] : "";
                        $file = new File();
                        $validateFile = $file->saveBase64Image(
                                $saveBase64Image, $validateLogin->id, User::MAIN_CATEGORY_LOGO
                        );

                        unset($post['photo']);

                        if ($validateFile) {
                            $data['User'] = $post;
                            if ($validateLogin->load($data) && $validateLogin->save()) {

                                $UserAuth = UserAuthType::findOne(['userId' => $validateLogin->id, 'registrationType' => 'email']);
                                if (!empty($UserAuth)) {
                                    $UserAuth->email = $post['email'];
                                    $UserAuth->save();
                                }
                                $response['error'] = false;
                                $response['msg'] = "Profile updated successfully.";
                            } else {
                                $response['error'] = true;
                                $response['msg'] = $validateLogin->getErrors();
                            }
                        } else {
                            $response['error'] = true;
                            $response['msg'] = "Failed to update profile.";
                        }
                    } else {
                        $response['error'] = true;
                        $response['msg'] = "Invalid User.";
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Invalid User, please try again.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionUpdatereader() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('firstName', 'lastName','displayname','email', 'telephone', 'tagLine', 'description', 'specialties');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    //$currentUserData = $validateLogin->getAttributes();
                    if ($validateLogin->role == User::ROLE_READER) {

                        $saveBase64Image = (isset($post['photo']) && $post['photo'] != "") ? $post['photo'] : "";
                        $file = new File();
                        $validateFile = $file->saveBase64Image(
                                $saveBase64Image, $validateLogin->id, User::MAIN_CATEGORY_LOGO
                        );

                        unset($post['photo']);

                        if ($validateFile) {
                            
                            $data['User'] = $post;
                            $validateLogin->displayname = $post['displayname'];
                            if ($validateLogin->load($data) && $validateLogin->save()) {
                                $validateLogin->saveSpecialties();
                                $UserAuth = UserAuthType::findOne(['userId' => $validateLogin->id, 'registrationType' => 'email']);
                                if (!empty($UserAuth)) {
                                    $UserAuth->email = $post['email'];
                                    $UserAuth->save();
                                }
                                $response['error'] = false;
                                $response['msg'] = "Profile updated successfully.";
                            } else {
                                $response['error'] = true;
                                $response['msg'] = $validateLogin->getErrors();
                            }
                        } else {
                            $response['error'] = true;
                            $response['msg'] = "Failed to update profile.";
                        }
                    } else {
                        $response['error'] = true;
                        $response['msg'] = "Invalid Reader.";
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Invalid Reader, please try again.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionGetcreditcards() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array();
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    $creditCardsResult = $validateLogin->getCreditCards();
                    $creditCards = array();
                    foreach ($creditCardsResult as $key => $card) {
                        $creditCards[$key] = $card->getAttributes();
                    }
                    $response['error'] = FALSE;
                    $response['bucks'] = $validateLogin->getGruviBucksAmount();
                    $response['profile'] = $currentUserData;
                    $response['profile']['displayname'] = (isset($currentUserData['displayname']) && $currentUserData['displayname'] != NULL) ? $currentUserData['displayname'] : "";
                    $response['creditcard'] = $creditCards;
                    $response['msg'] = 'Success.';
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Invalid user, please try again.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionBlockuser() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('userIds');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $userIds = explode(",", $post['userIds']);
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    //$userType = $currentUserData['role'];
                    //if ($userType == User::ROLE_READER || $userType == User::ROLE_ADMIN) {
                        foreach ($userIds as $key => $userId) {
                            $model = new UserRelation();
                            $model->senderId = $currentUserData['id'];
                            $model->recipientId = !empty($userId) ? $userId : null;
                            $model->messageId = !empty($post['messageId']) ? $post['messageId'] : null;
                            $model->action = UserRelation::ACTION_BLOCK;
                            $model->setScenario("create");
                            $model->create();
                            Message::banByUser($model->senderId, $model->recipientId);
                        }
                        $response['error'] = false;
                        $response['msg'] = "User blocked successfully.";
                    /*} else {
                        $response['error'] = true;
                        $response['msg'] = "You don't have permission to block user.";
                    }*/
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Invalid user, please try again.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionUnblockuser() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('userIds');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $userIds = explode(",", $post['userIds']);
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    //$userType = $currentUserData['role'];
                    $id = $currentUserData['id'];
                    //if ($userType == User::ROLE_READER || $userType == User::ROLE_ADMIN) {

                        foreach ($userIds as $key => $userId) {
                            $model = (new UserRelation())->findOne(['senderId' => $id, 'recipientId' => $userId]);
                            if (empty($model)) {
                                continue;
                            } else {
                                Message::unbanByUser($model->senderId, $model->recipientId);
                                $model->delete();
                            }
                        }
                        $response['error'] = false;
                        $response['msg'] = "User unblocked successfully.";
                    /*} else {
                        $response['error'] = true;
                        $response['msg'] = "You don't have permission to block user.";
                    }*/
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Invalid user, please try again.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionCallsStatistic() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('monthAndYear');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $currentUserData = $validateLogin->getAttributes();
                    $userType = $currentUserData['role'];
                    if ($userType == User::ROLE_READER) {
                        @list($monthName, $year) = explode(" ", $post['monthAndYear']);
                        $callsCount = 0;
                        $callDuration = 0;
                        $month = date('m', strtotime($monthName));
                        $callsStatistic = $this->call->getcallsStatistic($currentUserData['id'], $month, $year);
                        if (!empty($callsStatistic)) {
                            $callsCount = $callsStatistic['calls_count'];
                            $callDuration = ($callsStatistic['calls_duration'] > 0) ? round($callsStatistic['calls_duration'] / 60, 0) : 0;
                            $year = $callsStatistic['year'];
                            $month = $callsStatistic['month'];
                        }
                        $response['error'] = false;
                        $response['msg'] = "success";
                        $response['callsCount'] = $callsCount;
                        $response['callsDuration'] = (String) $callDuration;
                        $response['month'] = $month;
                        $response['year'] = $year;
                    } else {
                        $response['error'] = true;
                        $response['msg'] = "You don't have permission to get  calls statistic.";
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionSendmessage() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('readerId', 'userId', 'message');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $model = new Message();
                    $model->customerId = $post['userId'];
                    $model->readerId = $post['readerId'];
                    $model->message = $post['message'];
                    if ($model->save()) {
                        $response['error'] = FALSE;
                        $response['msg'] = 'Message sent successfully.';
                    } else {
                        $response['error'] = true;
                        $response['msg'] = $model->getErrors();
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }
    public function actionDeletemessage() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('messageId');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $messageId = $post['messageId'];
                    $message = Message::findOne($messageId);
                    $message->setStatus(Message::STATUS_DELETED);
                    $response['error'] = false;
                    $response['msg'] = 'Message deleted.';
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }
    public function actionReport() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();
        if (!empty($header)) {
            $header_fields = array('apiKey');
            $request_fields = array('messageId','reportedId','report_reason');
            $request_form_success = $this->verifyPost($header, $header_fields, $post, $request_fields);
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $model = new UserRelation();
                    $model->senderId      = $validateLogin->id;
                    $model->recipientId   = $post['reportedId'];
                    $model->messageId     = $post['messageId'];
                    $model->notes         = !empty($post['report_reason'])?$post['report_reason']:null;
                    $model->action        = UserRelation::ACTION_REPORT;
                    $model->setScenario("create");
                    if ($model->create()) {
                        $emailTo = Yii::$app->params['adminEmail'];
                        $reporter = $validateLogin->id;
                        $reported = User::find()->where(['id' => $post['reportedId']])->one();
                        Yii::$app->mailer->compose(
                            ['html' => 'reported_user_notification-html', 'text' => 'reported_user_notification-text'],
                            ['model'=> $model]
                        )
                        ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name . ' robot'])
                        ->setTo($emailTo)
                        ->setSubject('[madsap] Notification For Reported User')
                        ->send();
                        $response['error'] = FALSE;
                        $response['msg'] = "Success";
                    }else{
                        $response['error'] = true;
                        $response['msg'] = Site::get_error_summary($model->getErrors());
                    }
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }
    public function actionLogout() {
        $response = array();
        $post = Yii::$app->request->post();
        $header = $this->getHeaders();

        if (!empty($header)) {
            $request_fields = array('apiKey');
            $request_form_success = $this->verifyPost($header, $request_fields, array(), array());
            if (!$request_form_success) {
                $response['error'] = true;
                $response['msg'] = 'Required parameter missing.';
            } else {
                $apiKey = $header['APIKEY'];
                $validateLogin = $this->checkLogin($apiKey);
                if ($validateLogin) {
                    $validateLogin->isMobile = 0;
                    $validateLogin->activity = User::ACTIVITY_OFFLINE;
                    $validateLogin->activity_update = date('Y-m-d H:i:s');
                    $validateLogin->save();

                    $response['error'] = false;
                    $response['msg'] = "Logout successfully.";
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Login failed.';
                }
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        }
        echo json_encode($response);
    }

    public function actionSingupbackUP() {

        $post = Yii::$app->request->post();
        $request_fields = array('firstName', 'lastName', 'email', 'password', 'confirmPassword');
        $request_form_success = true;

        foreach ($request_fields as $request_field) {
            if (!isset($post[$request_field])) {
                $request_form_success = false;
                break;
            }
        }
        if (!$request_form_success) {
            $response['error'] = true;
            $response['msg'] = 'Required parameter missing.';
        } else {
            $request['User'] = $post;
            $user = new User();
            $user->setScenario('emailSignUp');

            if (!isset($post['dob']))
                $request['User']['dob'] = date('d-M-Y', strtotime('-20 years'));

            if (Yii::$app->request->post() && $user->load($request)) {

                $user->social = User::SOCIAL_EMAIL;
                $user->role = User::ROLE_USER;

                //echo $user->load(Yii::$app->request->post()).' | '.$user->validate().' | '.$user->socialLogin();exit;

                $checkUserResult = $user->socialLogin(true, false, [], TRUE, 'Singup');

                if (!empty($checkUserResult) && is_array($checkUserResult)) {
                    $response['error'] = true;
                    $response['msg'] = $post['email'] . ' has already been taken.';
                } elseif ($checkUserResult == true) {
                    GruviBucks::addBonusBucks($user->id);
                    $response['error'] = false;
                    $response['msg'] = 'Success Registration. Sign In, Please!';
                } else {
                    $response['error'] = true;
                    $response['msg'] = 'Failed to registering user.';
                }
            } else {
                $response['error'] = true;
                $response['msg'] = 'Invalid request.';
            }
        }
        echo json_encode($response);
    }

    protected function checkLogin($apiKey) {
        $response = $this->user->findUserByApiKey($apiKey);
        return $response;
    }

    protected function verifyPost($header = array(), $headerKey = array('apiKey'), $post = array(), $postkey = array()) {
        $return = true;
        $header_fields = $headerKey;
        $request_fields = $postkey;
        $header_form_success = true;
        $request_form_success = true;
        foreach ($header_fields as $header_field) {
            if (!isset($header[strtoupper($header_field)])) {
                $header_form_success = false;
                break;
            }
        }
        foreach ($request_fields as $request_field) {
            if (!isset($post[$request_field])) {
                $request_form_success = false;
                break;
            }
        }
        if ((!$header_form_success) || (!$request_form_success)) {
            $return = FALSE;
        }
        return $return;
    }

    protected function getHeaders($header_name = null) {
        $keys = array_keys($_SERVER);

        if (is_null($header_name)) {
            $headers = preg_grep("/^HTTP_(.*)/si", $keys);
        } else {
            $header_name_safe = str_replace("-", "_", strtoupper(preg_quote($header_name)));
            $headers = preg_grep("/^HTTP_${header_name_safe}$/si", $keys);
        }

        foreach ($headers as $header) {
            if (is_null($header_name)) {
                $headervals[substr($header, 5)] = $_SERVER[$header];
            } else {
                return $_SERVER[$header];
            }
        }

        return $headervals;
    }

    protected function searcharray($value, $key, $array) {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return true;
            }
        }
        return null;
    }
    
    protected function convert_time_zone($date_time, $from_tz, $to_tz) {
        $from_tz_object = new \DateTimeZone($from_tz);
        $time_object = new \DateTime($date_time, $from_tz_object);
        $time_object->setTimezone(new \DateTimeZone($to_tz));
        return $time_object->format('Y-m-d H:i:s');
    }

}
