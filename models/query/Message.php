<?php

namespace app\models\query;

use \yii\db\ActiveQuery;
use \app\models\StatusMessage;

/**
 * This is the ActiveQuery class for [[\app\models\Message]].
 *
 * @see \app\models\Message
 */
class Message extends ActiveQuery
{
    /**
     * @param $chatId
     * @param bool $dataProvider
     * @param int $limit
     * @param int $offset
     * @return \app\models\Message[]|\yii\db\ActiveQuery
     */
    public function byChatId($chatId, $dataProvider = false, $limit = 100, $offset = 0)
    {
        $query = $this->where('chatId = :c', [':c' => $chatId])
            ->limit($limit)
            ->orderBy(['id' => SORT_DESC])
            ->offset($offset);

        return $dataProvider ? $query : $query->all();
    }

    /**
     * @param $chatId
     * @param $userId
     * @return \app\models\Message|array|null
     */
    public function getLast($chatId, $userId = null)
    {
        $statuses = [StatusMessage::STATUS_READ, StatusMessage::STATUS_NEW];

        return $this->where('chatId = :c', [':c' => $chatId])
            ->joinWith(
                ['statusMessages' => function($subQuery) use ($userId, $statuses) {
                    /** @var ActiveQuery $subQuery */
                    $subQuery->alias('sm');

                    if(null !== $userId) {
                        $subQuery->andOnCondition(['sm.userId' => $userId]);
                    }

                    return $subQuery->andOnCondition(['status' => $statuses]);
                }],
                true,
                'JOIN'
            )
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    /**
     * @param $lastId
     * @param $chatId
     * @return \app\models\Message[]|array
     */
    public function byLastId($lastId, $chatId)
    {
        return $this->where('id > :id AND chatId = :c', [':id' => $lastId, ':c' => $chatId])->all();
    }

    /**
     * @inheritdoc
     * @return \app\models\Message[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Message|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}