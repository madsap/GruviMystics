<?php

namespace app\models\query;

use \yii\db\ActiveQuery;
use \app\models\ParticipantChat as ParticipantChatModel;

/**
 * This is the ActiveQuery class for [[\app\models\ParticipantChat]].
 *
 * @see \app\models\ParticipantChat
 */
class ParticipantChat extends ActiveQuery
{
    /**
     * @param $chatId
     * @return \app\models\ParticipantChat[]|array
     */
    public function allByChatId($chatId)
    {
        return $this
            ->where('chatId = :c AND status = :s', [':c' => $chatId, ':s' => ParticipantChatModel::STATUS_ACTIVE])
            ->all();
    }

    /**
     * @param $chatId
     * @param $userId
     * @return bool
     */
    public function isParticipant($chatId, $userId)
    {
        return (bool) $this->where(
                'chatId = :c AND userId = :u AND status = :s',
                [':c' => $chatId, ':u' => $userId, ':s' => ParticipantChatModel::STATUS_ACTIVE]
            )
            ->one();
    }

    /**
     * @inheritdoc
     * @return \app\models\ParticipantChat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\ParticipantChat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}