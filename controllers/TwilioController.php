<?php

namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Response;
use \yii\filters\VerbFilter;
use \app\components\MainController;
use \app\models\User;
use \app\models\Call;
use \app\models\Site;
use app\models\GruviBucks;
use Twilio\Jwt\ClientToken;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Twilio\Twiml;
use Twilio\Rest\Client;
use \yii\helpers\Url;
use yii\helpers\Html;
 
class TwilioController extends MainController
{
    public $defaultAction = 'index';


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'authenticate' => [
                'class'  => '\app\filters\AuthenticateFilter',
                'only' => ['chat-token']
            ]
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
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {            
        //if ($action->id == 'fallback' || $action->id == 'voice' || $action->id == 'status') {
            $this->enableCsrfValidation = false;
        //}

        return parent::beforeAction($action);
    }
    
/**
     * Displays homepage.
     *
     * @return string
     */
    public function actionOutOfGruviBucks()
    {

        \Yii::info('', 'OutOfGruviBucks');

        $response = new Twiml();
        $response->say("You're out of Gruvi Bucks. Thanks for calling!");
        header('Content-Type: text/xml');
        echo $response;
        exit;
    }
    
/**
     * Displays homepage.
     *
     * @return string
     */
    public function actionFallback()
    {

        \Yii::info('', 'twilioFallback');

        $response = new Twiml();
        $response->say("Thanks for calling!");
        header('Content-Type: text/xml');
        echo $response;
        exit;
    }
    
    

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionCallInfo()
    {

        // Your Account Sid and Auth Token from twilio.com/user/account
        $sid = "AC0808c0df342a7f9a1422012c59fa07f6";
        $token = "4090470bff37d20752f8e2430db0810d";
        $client = new Client($sid, $token);

        // Get an object from its sid. If you do not have a sid,
        // check out the list resource examples on this page
        
        
        
        try{
            $client = new Client(Yii::$app->params['twilio']['accountSid'], Yii::$app->params['twilio']['authToken']);

            $call = $client
                ->calls("CA2b9d0934f37f445969cd1dd30b14a91c")
                ->update(
                    array(
                        "url" => Url::to(["/twilio/out-of-gruvi-bucks"], true),
                        "method" => "POST"
                    )
                );
            print_r($call);
        }catch (\Exception $e) {
                //return Site::done_json([], 'error', $e->getMessage());
                Yii::warning($e);
                print_r($e->getMessage());
            }
        
         
        exit;
        
        
        
        
        
        
        
        
        $call = $client->calls("CA4fb90ba27db228fd13ee95fbb1c88d35")->fetch();
        print_r($call);
        exit;
/*
        $call = $client
            ->calls("CA49087e3caaba7dd8bfdd9441f488a0e2")
            ->update(
                array(
                    "url" => "http://demo.twilio.com/docs/voice.xml",
                    "method" => "POST"
                )
            );

        echo $call->to;*/
        

    $call = $client
    ->calls("CA49087e3caaba7dd8bfdd9441f488a0e2")
    ->update(
        array("status" => "completed")
    );

echo $call->direction;
        
        exit;
    }
    
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionStatus()
    {

        \Yii::info('', 'twilioStatus');

        $response = new Twiml();
        $response->say("status...");
        header('Content-Type: text/xml');
        echo $response;
        exit;
    }
    
/**
     * Displays homepage.
     *
     * @return string
     */
    public function actionVoice()
    {
        $options1 = [];
        $options1['callerId'] = "halcyon";
        $options2 = [];
        $options2["statusCallbackEvent"] = "initiated ringing answered completed";
                
        \Yii::info('', 'twilioVoice');

        if (empty($_REQUEST['PhoneNumber']))$this->say_and_exit("PhoneNumber is empty. ");
        if(empty($_REQUEST['ApplicationSid']) || $_REQUEST['ApplicationSid'] != Yii::$app->params['twilio']['appSid']){
            $this->say_and_exit("Can't verify call token authenticity");
        }
        if (empty($_REQUEST['CallSid']))$this->say_and_exit("Call id is empty. ");
        if (empty($_REQUEST['PhoneNumber']))$this->say_and_exit("Phone Number is empty. ");
        if (empty($_REQUEST['Caller']))$this->say_and_exit("Caller is empty. ");
        
        $number = $_REQUEST['PhoneNumber'];
        $caller = $_REQUEST['Caller'];
        $CallSid = $_REQUEST['CallSid'];
        
        $tmp = str_replace("client:u", "", $caller);
        $tmp = explode("_", $tmp);
        if(empty($tmp[0]))$this->say_and_exit("Caller info is wrong.");
        $user = (new User())->findIdentity($tmp[0]);
        
        if(empty($user->id) || $user->getNameForTwilio() != str_replace("client:", "", $caller))$this->say_and_exit("Can't verify your identity.");
        
        $current_balance = $user->getGruviBucksAmount();
        
        $tmp = explode("_", $number);
        if(empty($tmp[0]))$this->say_and_exit("Reader info is wrong. ");
        $reader = (new User())->findIdentity(str_replace("u", "", $tmp[0]));
        if(empty($reader->id) || $reader->getNameForTwilio() != $number)$this->say_and_exit("Can't verify reader's phone number.");
        
        if($reader->activity != User::ACTIVITY_ONLINE)$this->say_and_exit("the reader is not available now");
        if($reader->rate >= $current_balance)$this->say_and_exit("Not enough gruvi bucks to make this call");
        
       $activeCall = Call::getActiveCall($reader);
       if(!empty($activeCall))$this->say_and_exit("the reader is not available right now (busy line)");

       $activeCall = Call::getActiveCall($user);
       if(!empty($activeCall))$this->say_and_exit("the line is busy at the moment, please try again in a minute");
       
        $activeCall = new Call();
        $activeCall->customerId = $user->id;
        $activeCall->readerId = $reader->id;
        $activeCall->twilioCallId = $CallSid;
        $activeCall->save(false);
        
        $options1['callerId'] = "client:".$user->firstName;
        
        /*
        $gruvi_bucks = new GruviBucks();
        $gruvi_bucks->callId = $activeCall->id;
        $gruvi_bucks->userId = $user->id;
        $gruvi_bucks->amount = $reader->rate;
        $gruvi_bucks->log = 'initial charge';
        if(!$gruvi_bucks->charge()){
            $activeCall->abortActiveCall();
            $this->say_and_exit("Not enough gruvi bucks to make this call");
        }*/

        $response = new Twiml();
        $dial = $response->dial($options1);

        // wrap the phone number or client name in the appropriate TwiML verb
        // by checking if the number given has only digits and format symbols
        if (preg_match("/^[\d\+\-\(\) ]+$/", $number)) {
            $dial->number($number, $options2);
        } else {
            $dial->client($number, $options2);
        }

        
        if(!empty($say))$response->say($say);
        header('Content-Type: text/xml');
        echo $response;
        exit;
    }
    
    public function say_and_exit($say = ""){
        
        $response = new Twiml();
        if(empty($say))$say = "nothing to say";
            
        $response->say($say);
        header('Content-Type: text/xml');
        echo $response;
        exit;
        
    }
    
/**
     * Displays homepage.
     *
     * @return string
     */
    public function actionChatToken()
    {
        /*
        if (empty($_REQUEST['readerId']))Site::done_json([], 'error', 'reader id is empty');
            
        $reader = (new User())->findIdentity($_REQUEST['readerId']);
        if(empty($reader->id))Site::done_json([], 'error', '#404 not found');
        
        if($reader->activity != User::ACTIVITY_ONLINE && $reader->activity != User::ACTIVITY_SESSION){
            Site::done_json([], 'error', "the reader is not available now");
        }

        $chat = new Chat();
        $chat->customerId = Yii::$app->user->identity->id;
        $chat->readerId = $reader->id;
        $chat->save(false);*/
        
        
        
        $appName = 'Gruvi';
        $identity = Yii::$app->user->identity->firstName.' '.Yii::$app->user->identity->lastName;
        $deviceId = 'browser '.rand(1024, 4096);
        $endpointId = $appName . ':' . $identity . ':' . $deviceId;
        
        //$capability = new ClientToken(Yii::$app->params['twilio']['accountSid'], Yii::$app->params['twilio']['authToken']);
        $token = new AccessToken(
            Yii::$app->params['twilio']['accountSid'],
            Yii::$app->params['twilio']['apiKey'],
            Yii::$app->params['twilio']['apiKeySecret'],
            3600,
            $identity
        );
        
        // Create IP Messaging grant
        $ipmGrant = new ChatGrant();
        $ipmGrant->setServiceSid(Yii::$app->params['twilio']['chatSid']);
        $ipmGrant->setEndpointId($endpointId);

        $token->addGrant($ipmGrant);
        
        header('Content-type:application/json;charset=utf-8');
        echo json_encode(array(
            'identity' => $identity,
            'token' => $token->toJWT(),
        ));
        
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return 'twilio..';
    }

}
