<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "md_user_relation".
 *
 * @property integer $id
 * @property integer $senderId
 * @property integer $recipientId
 * @property integer $messageId
 * @property string $action
 * @property string $createAt
 *
 * @property Message $message
 * @property User $recipient
 * @property User $sender
 */
class UserRelation extends \yii\db\ActiveRecord
{
    public $messageText;
    const ACTION_BLOCK 	    = 'block';
    const ACTION_FOLLOW 	= 'follow';
    const ACTION_LIKE 	    = 'like';
    const ACTION_FAVORITE 	= 'favorite';
    
    public static $arrayStatuses    = [
        self::ACTION_BLOCK     => self::ACTION_BLOCK,
        self::ACTION_FOLLOW   => self::ACTION_FOLLOW,
        self::ACTION_LIKE   => self::ACTION_LIKE,
        self::ACTION_FAVORITE   => self::ACTION_FAVORITE,
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'md_user_relation';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['senderId', 'recipientId'], 'required'],
            [['senderId', 'recipientId', 'messageId'], 'integer'],
            [['action'], 'in', 'range' => self::$arrayStatuses],
            [['createAt', 'messageText'], 'safe'],
            [['messageId'], 'exist', 'skipOnError' => true, 'targetClass' => Message::className(), 'targetAttribute' => ['messageId' => 'id']],
            [['recipientId'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['recipientId' => 'id']],
            [['senderId'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['senderId' => 'id']],
			[
				['senderId'],
				'unique',
				'targetAttribute' => ['senderId', 'recipientId', 'action'],
				'message' => 'The combination of From User and To User has already been taken.',
				'on' => 'create'
			],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'senderId' => 'Sender ID',
            'recipientId' => 'Recipient ID',
            'messageId' => 'Message ID',
            'action' => 'Action',
            'createAt' => 'Create At',
        ];
    }

    // ======== Relations =======

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'messageId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipient()
    {
        return $this->hasOne(User::className(), ['id' => 'recipientId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'senderId']);
    }
    
    /**
     * @inheritdoc
     */
    public function create()
    {
        $so = ($this->save())?true:false;
        
        return $so;
    }
    
    /**
     * @inheritdoc
     */
    public static function check($post_id, $user_id, $action)
    {
        if(empty($post_id) || empty($user_id))return false;

        $aSo = static::findOne(['post_id' => $post_id, 'user_id' => $user_id, 'action' => $action]);
        return (!empty($aSo));
    }
    
	static function getSenderForSelect(){
		$db_expr = new \yii\db\Expression("CONCAT('#',md_user.`id`, ' ', COALESCE(`firstName`, ''), ' ', COALESCE(`lastName`, '')) as flname");
		return yii\helpers\ArrayHelper::map(UserRelation::find()->joinWith('sender')->select(['*', $db_expr])->asArray()->all(), 'id', 'flname');
	}
    
	static function getRecepientForSelect($senderId = 0){
		$db_expr = new \yii\db\Expression("CONCAT('#',md_user.`id`, ' ', COALESCE(`firstName`, ''), ' ', COALESCE(`lastName`, '')) as flname");
        $data = UserRelation::find()->joinWith('recipient')->select(['*', $db_expr]);
        if(!empty($senderId))$data->where(['senderId' => $senderId]);
        $data = $data->asArray()->all();
		return yii\helpers\ArrayHelper::map($data, 'id', 'flname');
	}
}
