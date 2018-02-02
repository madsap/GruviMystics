<?php

namespace app\models;

use Yii;
use \yii\helpers\Url;
use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use \app\components\StringHelper;

/**
 * This is the model class for table "md_call".
 *
 * @property integer $id
 * @property integer $customerId
 * @property integer $readerId
 * @property integer $duration
 * @property integer $callConnectionTime
 * @property integer $callAnswerTime
 * @property integer $callEndTime
 * @property string $status
 * @property string $createAt
 *
 * @property User $customer
 * @property User $reader
 */
class Call extends \yii\db\ActiveRecord
{
    const STATUS_CONNECTION = "Connecting";
    const STATUS_CONVERSATION = "Conversation";
    const STATUS_DONE = "Done";
    const STATUS_FAIL = "Fail";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'md_call';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerId', 'readerId', 'duration', 'callConnectionTime', 'callAnswerTime', 'callEndTime'], 'integer'],
            [['status'], 'required'],
            [['status'], 'string'],
            [['createAt'], 'safe'],
            ['duration', 'default', ['value' => 0]],
            ['status', 'default', ['value' => self::STATUS_CONNECTION]],
            [['customerId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['customerId' => 'id']],
            [['readerId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['readerId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customerId' => 'Customer ID',
            'readerId' => 'Reader ID',
            'duration' => 'Duration',
            'callConnectionTime' => 'Call Connection Time',
            'callAnswerTime' => 'Call Answer Time',
            'callEndTime' => 'Call End Time',
            'status' => 'Status',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customerId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReader()
    {
        return $this->hasOne(User::className(), ['id' => 'readerId']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getActiveCall($user_identity)
    {
            
        $pass = [];
        $pass['user_id'] = $user_identity->id;
        $pass['status1'] = self::STATUS_CONNECTION;
        $pass['status2'] = self::STATUS_CONVERSATION;
        $so = self::find()->where("(`customerId` = :user_id OR `readerId` = :user_id) AND (`status` = :status1 OR `status` = :status2)", $pass)->one();
        
        return $so;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function end()
    {
        $this->status = ($this->status == self::STATUS_CONVERSATION)?self::STATUS_DONE:self::STATUS_FAIL;
        $this->callEndTime = date("Y-m-d H:i:s");
        
        //echo $this->callEndTime;exit;
        
        if($this->status == self::STATUS_DONE){
            $answer_timestamp = strtotime($this->callAnswerTime);
            $end_timestamp = strtotime($this->callEndTime);
            if($answer_timestamp > time() - 24*3600 && $end_timestamp > time() - 24*3600){
                $duration = $end_timestamp - $answer_timestamp;
                if($duration < 24*3600)$this->duration = $duration;
            }
        }
        
        $this->save(false);
    }
    
    public function abortActiveCall(){
        
        try{
            $client = new Client(Yii::$app->params['twilio']['accountSid'], Yii::$app->params['twilio']['authToken']);

            $call = $client
                ->calls($this->twilioCallId)
                ->update(
                    array(
                        "url" => Url::to(["/twilio/out-of-gruvi-bucks"], true),
                        "method" => "POST"
                    )
                );
        }catch (\Exception $e) {
            
            \Yii::info($e->getMessage(), 'can not redirect');
            
                $call = $client
                ->calls($this->twilioCallId)
                ->update(
                    array("status" => "completed")
                );
               // return Site::done_json([], 'error', $e->getMessage());
               // Yii::warning($e);
            }

        $this->end();
        
/*
    $call = $client
    ->calls("CA49087e3caaba7dd8bfdd9441f488a0e2")
    ->update(
        array("status" => "completed")
    );*/
        
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function answer()
    {
        if($this->status != self::STATUS_CONNECTION)return false;
        
        $this->status = self::STATUS_CONVERSATION;
        $this->callAnswerTime = date("Y-m-d H:i:s");
        $this->save(false);
        return true;
    }
    
    /**
     * @param       $data
     * @param null  $names
     * @param array $except
     * @return array
     */
    public function getAllPublicAttributes($data, $names = null, $except = [])
    {
        $result = [];

        /** @var User $user */
        foreach ($data as $row) {
            //$row->myId = $this->myId;
            $result[]   = $row->getPublicAttributes($names, $except);
        }

        return $result;
    }
    

    /**
     * @param null  $names
     * @param array $except
     * @return array
     */
    public function getPublicAttributes($names = null, $except = [])
    {
        if (empty($except)) {
            $except = [];
        }

        $attributes = parent::getAttributes($names, $except);

       // $attributes['customer'] = (!empty($this->customer))?$this->customer->getPublicAttributes():[];
       // $attributes['reader'] = (!empty($this->reader))?$this->reader->getPublicAttributes():[];

        StringHelper::removeNull($attributes);

        return $attributes;
    }
    
    
    public function renderCallPopup($myId, $customer = null, $reader = null){//pass here in case of new call. due to readonly property

       if(empty($customer))$customer = $this->customer;
       if(empty($reader))$reader = $this->reader;
       
       $balance = $this->customer->getGruviBucksAmount();
      
       $currentCredit = !empty($this->reader->rate)?floor($balance/$this->reader->rate):"0";
       
       $template = ($myId == $this->readerId)?'reader':'customer';
       
       $pass = ['customer' => $customer, 'reader' => $reader, 'activeCall' => $this, 'currentCredit' => $currentCredit];
       
       return Yii::$app->controller->renderPartial('@app/views/call/conversation_'.$template, $pass);
    }
    
    public function calculateDuration(){
        
        $diff = time() - strtotime($this->callAnswerTime);
        return date("H:i:s", $diff);
        
    }
    
    public static function getcallsStatistic($reader_id,$month,$year)
    {   
        $sql = "SELECT COUNT(*) as calls_count, SUM(`duration`) as calls_duration FROM `md_call` WHERE (`readerId` = :userId) AND (YEAR(`createAt`) = :year) AND (MONTH(`createAt`) = :month)";
        $callsStatistic = Yii::$app->getDb()->createCommand($sql, [':userId' => $reader_id, ':year' => $year,':month' => $month])->queryOne();
        $callsStatistic['year'] = $year;
        $callsStatistic['month'] = $month;
        return $callsStatistic;
    }
    
}
