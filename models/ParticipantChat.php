<?php

namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;
use \app\models\query\ParticipantChat as ParticipantChatQuery;

/**
 * This is the model class for table "{{%participant_chat}}".
 *
 * @property integer $id
 * @property integer $chatId
 * @property integer $userId
 * @property integer $lastMessageId
 * @property string $status
 * @property string $createAt
 *
 * @property Chat $chat
 * @property User $user
 */
class ParticipantChat extends ActiveRecord
{
    const STATUS_ACTIVE             = 'active';
    const STATUS_INACTIVE           = 'inactive';

    public static $arrayStatuses    = [
        self::STATUS_ACTIVE         => self::STATUS_ACTIVE,
        self::STATUS_INACTIVE       => self::STATUS_INACTIVE,
    ];

    /**
     * @param $chatId
     * @param $userId
     * @param bool|true $check
     */
    public function create($chatId, $userId, $check = true)
    {
        if (true === $check) {
            $participantsChats = ParticipantChat::find()
                ->where('chatId = :c AND userID = :u', [':c' => $chatId, ':u' => $userId])
                ->one();

            if (null !== $participantsChats) {
                $this->attributes = $participantsChats->attributes;
                $this->id = $participantsChats->id;

                return;
            }
        }

        $this->_saveInfo($chatId, $userId);
    }

    /**
     * @param $userId
     * @param $status
     */
    public static function switchStatus($userId, $status)
    {
        if ($status == self::STATUS_ACTIVE) {
            $oldStatus = self::STATUS_INACTIVE;
            $counter = 1;
        } else {
            $oldStatus = self::STATUS_ACTIVE;
            $counter = -1;
        }

        $myChats = self::find()->where(['userId' => $userId, 'status' => $oldStatus]);

        /** @var ParticipantChat $myChat */
        foreach ($myChats as $myChat) {
            Chat::updateAllCounters(['countParticipants' => $counter], 'id = :id', [':id' => $myChat->chatId]);
            $myChat->status = $status;
            $myChat->update(true, ['status']);
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%participant_chat}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chatId', 'userId'], 'required'],
            [['chatId', 'userId', 'lastMessageId'], 'integer'],
            [['status'], 'in', 'range' => self::$arrayStatuses],
            [['createAt'], 'safe'],
            [
                ['chatId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Chat::className(),
                'targetAttribute' => ['chatId' => 'id']
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
            'chatId'    => Yii::t('app', 'Chat ID'),
            'userId'    => Yii::t('app', 'User ID'),
            'status'    => Yii::t('app', 'Status'),
            'createAt'  => Yii::t('app', 'Create At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(Chat::className(), ['id' => 'chatId']);
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
     * @return \app\models\query\ParticipantChat the active query used by this AR class.
     */
    public static function find()
    {
        return new ParticipantChatQuery(get_called_class());
    }

    /**
     * @param $chatId
     * @param $userId
     */
    private function _saveInfo($chatId, $userId)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->status = self::STATUS_ACTIVE;
        if ($this->save()) {
            Chat::updateAllCounters(['countParticipants' => 1], 'id = :id', [':id' => $this->chatId]);
        }
    }
}