<?php

namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;
use \app\models\query\Chat as ChatQuery;

/**
 * This is the model class for table "{{%chat}}".
 *
 * @property integer            $id
 * @property string             $cHash
 * @property integer            $countParticipants
 * @property integer            $lastMessageId
 * @property string             $lastMessageTime
 * @property string             $status
 * @property string             $createAt
 *
 * @property integer            $myId
 * @property array              $participantsArray
 * @property integer            $participantId
 * @property Message[]          $messages
 * @property StatusMessage      $newMessage
 * @property ParticipantChat[]  $participantChats
 * @property ParticipantChat[]  $myChats
 */
class Chat extends ActiveRecord
{
    const STATUS_ACTIVE             = 'active';
    const STATUS_INACTIVE           = 'inactive';

    public $myId;
    public $participantsArray       = [];
    public $participantId;

    public static $arrayStatuses    = [
        self::STATUS_ACTIVE     => self::STATUS_ACTIVE,
        self::STATUS_INACTIVE   => self::STATUS_INACTIVE,
    ];

    /**
     * @param $interlocutorId
     * @param $userId
     * @param bool $check
     * @return bool
     */
    public function create($interlocutorId, $userId, $check = true)
    {
        if (true === $check) {
            $chat = Chat::find()->byInterlocutorId($interlocutorId, $userId);
            if (null !== $chat) {
                $this->attributes = $chat->attributes;
                $this->id = $chat->id;

                return true;
            }
        }

        $this->setScenario('create');
        $this->_saveInfo($interlocutorId, $userId);

        return true;
    }

    /**
     * @return int
     */
    public function remove()
    {
        return Message::markAsDeleted($this->id, $this->myId);
    }

    /**
     * @param $chat
     * @param $result
     * @return boolean
     */
    private function _addLastMessage($chat, &$result)
    {
        $lastMessage = Message::findOne($chat->lastMessageId);
        if (null !== $lastMessage) {
            $lastMessage->myId = $this->myId;
            $result['messages'][] = $lastMessage->getPublicAttributes();

            return true;
        }

        return false;
    }

    /**
     * @param $chats
     * @param null $names
     * @param array $except
     * @return array
     */
    public function getAllPublicAttributes($chats, $names = null, $except = [])
    {
        $result = ['chats' => [], 'users' => [], 'messages' => []];

        $i = 0;

        /** @var Chat $chat */
        foreach ($chats as $chat) {
            $chat->myId = $this->myId;
            $chatInfo = $chat->getPublicAttributes($names, $except);

            if (true === $this->_addLastMessage($chat, $result)) {
                $result['chats'][$i] = $chatInfo;
                $this->participantsArray = array_merge($this->participantsArray, $chat->participantsArray);
                ++$i;
            }
        }

        $users = User::find()->where(['id' => $this->participantsArray])->all();
        $result['users'] = (new User)->getAllPublicAttributes($users);

        return $result;
    }

    /**
     * @param null $names
     * @param array $except
     * @return array
     */
    public function getPublicAttributes($names = null, $except = [])
    {
        if (empty($except)) {
            $except = ['id', 'createAt'];
        }

        $attributes = parent::getAttributes($names, $except);

        $participants = [];

        $this->lastMessageId = null;

        foreach ($this->myChats as $participant) {
            if ($this->myId != $participant->userId) {
                $participants[] = $participant->userId;
                if (empty($this->participantsArray[$participant->userId])) {
                    $this->participantsArray[$participant->userId] = $participant->userId;
                }
            } else {
                $this->lastMessageId = $participant->lastMessageId;
            }
        }

        $attributes['newMessage'] = empty($this->newMessage) ? 0 : 1;

        $attributes['participants'] = $participants;

        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%chat}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cHash', 'countParticipants', 'status'], 'required', 'on' => ['create']],
            [['cHash'], 'required', 'on' => ['markAsRead', 'delete']],
            [['countParticipants', 'lastMessageId'], 'integer'],
            [['myId', 'participantId'], 'integer'],
            [['cHash'], 'checkParticipant', 'on' => ['markAsRead', 'delete']],
            [['participantId'], 'required', 'on' => ['findByUser']],
            [['cHash'], 'required', 'on' => ['view']],
            [['status'], 'in', 'range' => self::$arrayStatuses],
            [['lastMessageTime', 'createAt'], 'safe'],
            [['cHash'], 'string', 'max' => 64],
            [['cHash'], 'unique', 'except' => ['view', 'markAsRead', 'delete']],
        ];
    }

    /**
     * @return void
     */
    public function checkParticipant()
    {
        /** @var Chat $chat */
        $chat = Chat::find()->byCHash($this->cHash, $this->myId);
        if (null === $chat) {
            $this->addError('cHash', 'You are not a participant in this chat');
        } else {
            $this->id = $chat->id;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', 'ID'),
            'cHash'             => Yii::t('app', 'Chat Hash'),
            'countParticipants' => Yii::t('app', 'Count Participants'),
            'lastMessageTime'   => Yii::t('app', 'Last Message Time'),
            'status'            => Yii::t('app', 'Status'),
            'createAt'          => Yii::t('app', 'Create At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['chatId' => 'id'])->alias('me');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewMessage()
    {
        return $this->hasOne(StatusMessage::className(), ['messageId' => 'id'])
            ->onCondition(['sm.status' => StatusMessage::STATUS_NEW])
            ->alias('sm')
            ->via('messages');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantChats()
    {
        return $this->hasMany(ParticipantChat::className(), ['chatId' => 'chatId'])
            ->alias('pc2')
            ->andOnCondition(['pc2.status' => ParticipantChat::STATUS_ACTIVE])
            ->via('myChats');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMyChats()
    {
        return $this->hasMany(ParticipantChat::className(), ['chatId' => 'id'])
            ->alias('pc1')
            ->andOnCondition(['pc1.status' => ParticipantChat::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\Chat the active query used by this AR class.
     */
    public static function find()
    {
        return new ChatQuery(get_called_class());
    }

    /**
     * @param $interlocutorId
     * @param $userId
     */
    private function _saveInfo($interlocutorId, $userId)
    {
        $this->cHash                = Yii::$app->security->generateRandomString(64);
        $this->countParticipants    = 0;
        $this->status               = self::STATUS_ACTIVE;
        if ($this->save()) {
            $userParticipant = new ParticipantChat();
            $userParticipant->create($this->id, $userId);
            $interlocutorParticipant = new ParticipantChat();
            $interlocutorParticipant->create($this->id, $interlocutorId);
        }
    }
}