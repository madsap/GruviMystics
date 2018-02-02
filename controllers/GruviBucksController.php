<?php

namespace app\controllers;

use Yii;
use app\models\Site;
use app\models\GruviBucks;
use app\models\search\GruviBucks as GruviBucksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserCreditCard;
use \yii\helpers\Url;
use app\models\User;

/**
 * GruviBucksController implements the CRUD actions for GruviBucks model.
 */
class GruviBucksController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'authenticate' => [
                'class'  => '\app\filters\AuthenticateFilter',
                'except' => ['paypal', 'paypal2', 'paypal-return', 'paypal-cancel', 'paypal-notify']
            ]
        ];
    }
    
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {            
        if ($action->id == 'paypal' || $action->id == 'paypal-return' || $action->id == 'paypal-cancel' || $action->id == 'paypal-notify') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all GruviBucks models.
     * @return mixed
     */
    public function actionPaypal()
    {
        echo '    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">  
    <div>  
        <label for="amount">Amount for transfer</label>  
        <input id="amount" type="text" />  
    </div>  
    <input type="hidden" name="cmd" value="_donations" />  
    <input type="hidden" name="charset" value="utf-8" />  
    <input type="hidden" name="bussiness" value="FullHD15-facilitator@gmail.com" />  
    <input type="hidden" name="item_name" value="Item short name" />  
    <input type="hidden" name="currency_code" value="USD" />  
    <input type="hidden" name="undefined_quantity" value="1" />  
    <input type="hidden" name="return" value="'.Url::to(["/gruvi-bucks/paypal-return"], true).'" />  
    <input type="hidden" name="cancel_return" value="'.Url::to(["/gruvi-bucks/paypal-cancel"], true).'" />  
    <input type="hidden" name="notify_url" value="'.Url::to(["/gruvi-bucks/paypal-notify"], true).'" />  
    <input type="hidden" name="custom" value="userId:1|orderId:25" />  
    <input type="hidden" name="button_subtype" value="services" />  
    <input type="hidden" name="no_note" value="1" />  
    <input type="hidden" name="no_shipping" value="1" />  
    <input type="hidden" name="rm" value="" />  
    <div>  
        <input type="submit" value="Transfer" />  
    </div>  
</form>  ';
    }    
    
    /**
     * Lists all GruviBucks models.
     * @return mixed
     */
    public function actionPaypalReturn()
    {
        
        
        
        
        echo "ok";exit;
        $php_input = file_get_contents('php://input');
        \Yii::info('***'.$php_input.'***', 'paypalReturn');
        
        //$php_input = '{"id":"WH-8VY30125BW147373B-4E4969335K8102845","event_version":"1.0","create_time":"2017-09-18T17:53:44.329Z","resource_type":"sale","event_type":"PAYMENT.SALE.COMPLETED","summary":"Payment completed for $ 20.0 USD","resource":{"id":"9XW68550TV133741E","state":"completed","amount":{"total":"20.00","currency":"USD","details":{"subtotal":"20.00"}},"payment_mode":"INSTANT_TRANSFER","protection_eligibility":"INELIGIBLE","transaction_fee":{"value":"0.88","currency":"USD"},"invoice_number":"5_1505755970","custom":"5","parent_payment":"PAY-5N4925364C8863008LHAAP2Y","create_time":"2017-09-18T17:53:01Z","update_time":"2017-09-18T17:53:01Z","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/sale/9XW68550TV133741E","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/sale/9XW68550TV133741E/refund","rel":"refund","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-5N4925364C8863008LHAAP2Y","rel":"parent_payment","method":"GET"}]},"links":[{"href":"https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-8VY30125BW147373B-4E4969335K8102845","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-8VY30125BW147373B-4E4969335K8102845/resend","rel":"resend","method":"POST"}]}';
        $paypalTransaction = json_decode($php_input, true);
        //print_r($aObj);
        //exit;
        
        
        
        $username=Yii::$app->params['paypal'][Yii::$app->params['paypal']['environment']]['ClientID'];
        $password=Yii::$app->params['paypal'][Yii::$app->params['paypal']['environment']]['secret'];
        
        /*
        //in case wee need access_token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://api.sandbox.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        $result=curl_exec ($ch);
        
        $aData = json_decode($result, true);

        if(empty($aData['access_token'])){
            echo 'no token..';
        }
//        Array
//            (
//                [scope] => https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/applications/webhooks openid
//                [nonce] => 2017-09-18T14:00:10Z46mg_5O4xmhTE256t8pX4H0OnPXJHOBLtmxTKhlHx1I
//                [access_token] => A21AAEVGW80oNj1Saq2Xrk2B_8VGCbVQZZLK2V79v_zN08r9AmB5ogQGKgQGoT-pCxwhUYJhbCbwoP5-yUtJ3BqoxGT-ei_xQ
//                [token_type] => Bearer
//                [app_id] => APP-80W284485P519543T
//                [expires_in] => 31410
//            )
        */
        
        
        //in case wee need verify transanction (double)
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypalTransaction['links'][0]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$aData['access_token'];
            ));
        $php_input=curl_exec ($ch);
        $paypalTransaction = json_decode($php_input, true);
        */
        

        if(empty($_SERVER['HTTP_PAYPAL_TRANSMISSION_ID'])){
            \Yii::info('***no paypal***', 'paypalCancel');
            echo 'no paypal';
            exit;
        }
        
        if(empty($paypalTransaction['resource']['id'])){
            \Yii::info('***no resource id***', 'paypalCancel');
            echo 'no resource id';
            exit;
        }
        
        if(empty($paypalTransaction['resource']['state']) || $paypalTransaction['resource']['state'] != 'completed'){
            \Yii::info('***state is not completed***', 'paypalCancel');
            echo 'state is not completed';
            exit;
        }
        
        if(empty($paypalTransaction['resource']['amount']['total'])){
            \Yii::info('***total is not set***', 'paypalCancel');
            echo 'total is not set';
            exit;
        }
        
        
        if(empty($paypalTransaction['resource']['amount']['currency']) || $paypalTransaction['resource']['amount']['currency'] != 'USD'){
            \Yii::info('***currency is not USD***', 'paypalCancel');
            echo 'currency is not USD';
            exit;
        }
        
        if(empty($paypalTransaction['resource']['custom'])){
            \Yii::info('***custom is not set***', 'paypalCancel');
            echo 'custom is not set';
            exit;
        }

        
        $model = new GruviBucks();
        $model->userId = $paypalTransaction['resource']['custom'];
        $model->paypalTransaction = $paypalTransaction['resource']['id'];
        $model->amount = $paypalTransaction['resource']['amount']['total'];
        $model->log = "";
        $model->addGruviBucks();
        
        \Yii::info('***OK***', 'paypalCancel');
        echo 'ok';exit;
    }

    /**
     * Lists all GruviBucks models.
     * @return mixed
     */
    public function actionPaypalCancel()
    {
        \Yii::info('', 'paypalCancel');
        echo 'ok';exit;
    }
    
    /**
     * Lists all GruviBucks models.
     * @return mixed
     */
    public function actionPaypalNotify()
    {
        \Yii::info('', 'paypalNotify');
        echo 'ok';exit;
    }
    
    /**
     * Lists all GruviBucks models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(!User::isAdmin())return $this->redirect(['/']);
        $searchModel = new GruviBucksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new GruviBucks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new GruviBucks();

        if (isset($_REQUEST['amount']) || isset($_REQUEST['creditCardId'])) {
            
            if(empty($_REQUEST['creditCardId']))return Site::done_json([], 'error', 'Choose a payment type');
            $amount = (float)$_REQUEST['amount'];
            if($amount < 1)return Site::done_json([], 'error', 'amount should be >= $1.00');
            
            $card = (new UserCreditCard())->findOne(['userId' => Yii::$app->user->identity->id, 'id' => $_REQUEST['creditCardId']]);
            if(empty($card->token))return Site::done_json([], 'error', 'credit card not found');
            
            try {
                \Stripe\Stripe::setApiKey(Yii::$app->params['stripe']['secretKey']);

                $transaction = \Stripe\Charge::create(array(
                  "amount" => $amount*100,
                  "currency" => "usd",
                  "customer" => $card->token, // obtained with Stripe.js
                  "description" => "Charge for customer #".Yii::$app->user->identity->id." ".Yii::$app->user->identity->email
                ));
            }catch (\Exception $e) {
                    return Site::done_json([], 'error', $e->getMessage());
                   // Yii::warning($e);
                }
            
            if(!empty($transaction->failure_code) || !empty($transaction->failure_message)){
               return Site::done_json([], 'error', $transaction->failure_code.' '.$transaction->failure_message);
            }
           
            if(empty($transaction->id))return Site::done_json([], 'error', "Something went wrong with stripe");
            
            $model->userId = Yii::$app->user->identity->id;
            $model->creditCardId = $_REQUEST['creditCardId'];
            $model->stripeTransaction = $transaction->id;
            $model->amount = $transaction->amount/100;
            $model->log = "";
            $model->addGruviBucks();
            
            return Site::done_json(['html' => '']);
            
        } else {
            
            $creditCards = Yii::$app->user->identity->getCreditCards();
            
            return $this->render('add', [
                'model' => $model,
                'creditCards' => $creditCards
            ]);
        }
    }

    /**
     * Deletes an existing GruviBucks model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(!User::isAdmin())return $this->redirect(['/']);
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the GruviBucks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GruviBucks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GruviBucks::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
