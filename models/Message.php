<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use \app\models\User;
/**
 * This is the model class for table "md_message".
 *
 * @property integer $id
 * @property integer $customerId
 * @property integer $readerId
 * @property string $message
 * @property string $status
 * @property string $createAt
 *
 * @property User $customer
 * @property User $reader
 */
class Message extends \yii\db\ActiveRecord
{
    const TABLE_NAME                = 'Message';
    const ATTACHMENT_NAME           = 'attachments';
    const MAIN_CATEGORY_ATTACHMENT  = 'attachment';
    
    const STATUS_VISIBLE = "visible";
    const STATUS_DELETED = "deleted";
    const STATUS_BANNED = "banned";
    
    public static $arrayStatuses    = [
        self::STATUS_VISIBLE     => self::STATUS_VISIBLE,
        self::STATUS_DELETED   => self::STATUS_DELETED,
        self::STATUS_BANNED   => self::STATUS_BANNED,
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'md_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerId', 'readerId'], 'integer'],
            [['status'], 'in', 'range' => self::$arrayStatuses],
            [['message', 'readerId'], 'required'],
            [['createAt', 'changeAt'], 'safe'],
            [['customerId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['customerId' => 'id']],
            [['readerId'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['readerId' => 'id']],
            ['customerId', 'validateCustomer'],
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function validateCustomer($attribute, $params, $validator)
    {
        if(User::isAdmin())return true;
		$condition = 'senderId = :u1 AND recipientId = :u2 AND `action` = :action';
		$params = [':u1' => $this->readerId, ':u2' => $this->$attribute, ':action' => UserRelation::ACTION_BLOCK];
		$so = (new UserRelation())->find()->where($condition, $params)->one();
        if(!empty($so->id)){
            $this->addError($attribute, 'Reader has blocked you from posting to this chat');
            return false;
        }
        
        return true;
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
            'message' => 'Message',
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
    
    public function editable($myId){
        return ($this->customerId == $myId || $this->readerId == $myId);
    }
    
    public function setStatus($new_status){
        $this->status = $new_status;
        $this->changeAt = new Expression('NOW()');
        $this->save(true, ['status', 'changeAt']);
    }
    
    public static function banByUser($readerId, $customerId){
        $set = ['status' => Message::STATUS_BANNED, 'changeAt' => new Expression('NOW()')];
        $quote = ['customerId' => $customerId, 'readerId' => $readerId, 'status2' => Message::STATUS_VISIBLE];
        $where = 'customerId = :customerId AND readerId = :readerId AND `status` = :status2';
        Message::updateAll($set, $where, $quote);
    }
    
    public static function unbanByUser($readerId, $customerId){
        $set = ['status' => Message::STATUS_VISIBLE];
        $quote = ['customerId' => $customerId, 'readerId' => $readerId, 'status2' => Message::STATUS_BANNED];
        $where = 'customerId = :customerId AND readerId = :readerId AND `status` = :status2';
        Message::updateAll($set, $where, $quote);
    }
    
	static function getArrayForSelect(){
		return yii\helpers\ArrayHelper::map(Message::find()->select(['*'])->asArray()->all(), 'message');
	}
    
	static function getReaderForSelect(){
		$db_expr = new \yii\db\Expression("CONCAT('#',md_user.`id`, ' ', COALESCE(`firstName`, ''), ' ', COALESCE(`lastName`, '')) as flname");
		return yii\helpers\ArrayHelper::map(self::find()->joinWith('reader')->select(['*', $db_expr])->asArray()->all(), 'id', 'flname');
	}
    
	static function getCustomerForSelect($readerId = 0){
		$db_expr = new \yii\db\Expression("CONCAT('#',md_user.`id`, ' ', COALESCE(`firstName`, ''), ' ', COALESCE(`lastName`, '')) as flname");
        $data = self::find()->joinWith('customer')->select(['*', $db_expr]);
        if(!empty($senderId))$data->where(['readerId' => $readerId]);
        $data = $data->asArray()->all();
		return yii\helpers\ArrayHelper::map($data, 'id', 'flname');
	}

    public function amIOwner() {
        // Because it's a public chat room 'owned' by the reader, only readers can 'own' the messages...
        // ...so basically this means is the reader as sessionUser in the chat
        $sessionUser = Yii::$app->user->identity;
        if ( empty($sessionUser) ) {
            return false;
        }
        $amIReader = 'reader' == $sessionUser->role;
        $is = $amIReader && ($this->readerId == $sessionUser->id);
        return $is;
    }

    // am I the sender of a message
    public function amISender() {
        $sessionUser = Yii::$app->user->identity;
        if ( empty($sessionUser) ) {
            return false;
        }
        switch ($sessionUser->role) {
            case 'reader':
                $is =    ($this->customerId == $sessionUser->id)  // customer is same as reader in this case (yes this is weird)
                      && ($this->readerId == $sessionUser->id); 
                break;
            case 'user':
                $is = ($this->customerId == $sessionUser->id); // customer is same as user
                break;
            default:
                $is = false;
        }
        return $is;
    }

    // am I the receiver of a message
    public function amIReceiver() {
        $sessionUser = Yii::$app->user->identity;
        switch ($sessionUser->role) {
            case 'reader':
                $is =  ($this->readerId == $sessionUser->id); 
                break;
            case 'user':
                $is = false; // because it's a public chat, no single user/customer is a receiver
                break;
            default:
                $is = false;
        }
        return $is;
    }

}
