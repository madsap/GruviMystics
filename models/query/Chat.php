<?php

namespace app\models\query;

use \yii\db\ActiveQuery;
use \app\models\ParticipantChat;

/**
 * This is the ActiveQuery class for [[\app\models\Chat]].
 *
 * @see \app\models\Chat
 */
class Chat extends ActiveQuery
{
    /**
     * @param $cHash
     * @param $participantId
     * @return \app\models\Chat|array|null
     */
    public function byCHash($cHash, $participantId = null)
    {
        $query = $this->where('cHash = :c AND countParticipants > 1', [':c' => $cHash]);

        if(null !== $participantId) {
            $query->joinWith(
                ['participantChats' => function($subQuery) use ($participantId) {
                    /** @var \yii\db\ActiveQuery $subQuery */
                    return $subQuery->onCondition(['pc2.userId' => $participantId]);
                }],
                true,
                'JOIN');
        }

        return $query->one();
    }

    /**
     * @param $interlocutorId
     * @param $userId
     * @return \app\models\Chat|array|null
     */
    public function byInterlocutorId($interlocutorId, $userId)
    {
        return $this->where('countParticipants = 2')
            ->alias('t')
            ->join(
                'JOIN',
                ParticipantChat::tableName() . ' AS m1',
                'm1.chatId = t.id AND m1.userId = :iu',
                [':iu' => $interlocutorId]
            )
            ->join(
                'JOIN',
                ParticipantChat::tableName() . ' AS m2',
                'm2.chatId = t.id AND m2.userId = :u',
                [':u' => $userId]
            )
            ->one();
    }

    /**
     * @inheritdoc
     * @return \app\models\Chat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Chat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}