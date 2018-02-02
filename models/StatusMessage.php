<?php

namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;
use \app\models\query\StatusMessage as StatusMessageQuery;

/**
 * This is the model class for table "{{%status_message}}".
 *
 * @property integer $id
 * @property integer $messageId
 * @property integer $userId
 * @property string $status
 * @property string $createAt
 *
 * @property Message $message
 * @property User $user
 */
class StatusMessage extends ActiveRecord
{
    const STATUS_NEW            = 'new';
    const STATUS_READ           = 'read';
    const STATUS_DELETE         = 'delete';

    public static $arrayStatuses = [
        self::STATUS_NEW    => self::STATUS_NEW,
        self::STATUS_READ   => self::STATUS_READ,
        self::STATUS_DELETE => self::STATUS_DELETE,
    ];

    /**
     * @param $messageId
     * @param $chatId
     * @param $authorId
     * @return ParticipantChat[]|array
     */
    public static function newMessage($messageId, $chatId, $authorId)
    {
        $participants = ParticipantChat::find()->allByChatId($chatId);
        if (!empty($participants)) {
            foreach ($participants as $participant) {
                $status = self::STATUS_NEW;

                if ($participant->userId == $authorId) {
                    $status = self::STATUS_READ;
                }

                $statusMessage = new StatusMessage();
                $statusMessage->saveInfo($messageId, $participant->userId, $status);
            }
        }

        return $participants;
    }

    /**
     * @return bool
     */
    public function changeStatus()
    {
        if (in_array($this->status, [self::STATUS_READ, self::STATUS_DELETE])) {
            $statusMessage = StatusMessage::find()->byMessageAndUser($this->messageId, $this->userId);

            if (null !== $statusMessage && !in_array($statusMessage->status, [$this->status, self::STATUS_DELETE])) {
                $statusMessage->status = $this->status;
                if ($statusMessage->update(false, ['status'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $messageId
     * @param $userId
     * @param $status
     */
    public function saveInfo($messageId, $userId, $status)
    {
        $this->messageId = $messageId;
        $this->userId = $userId;
        $this->status = $status;
        if ($this->save()) {
            //TODO
            //create activities
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%status_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['messageId', 'userId'], 'required'],
            [['messageId', 'status'], 'required', 'on' => ['changeStatus']],
            [['messageId', 'userId'], 'integer'],
            [['status'], 'in', 'range' => self::$arrayStatuses],
            [['status'], 'in', 'range' => [self::STATUS_DELETE, self::STATUS_READ], 'on' => ['changeStatus']],
            [['createAt'], 'safe'],
            [
                ['messageId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Message::className(),
                'targetAttribute' => ['messageId' => 'id']
            ],
            [
                ['userId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['userId' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => Yii::t('app', 'ID'),
            'messageId' => Yii::t('app', 'Message ID'),
            'userId'    => Yii::t('app', 'User ID'),
            'status'    => Yii::t('app', 'Status'),
            'createAt'  => Yii::t('app', 'Create At'),
        ];
    }

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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\StatusMessage the active query used by this AR class.
     */
    public static function find()
    {
        return new StatusMessageQuery(get_called_class());
    }
}