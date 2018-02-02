<?php

namespace app\models\query;

use \yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\StatusMessage]].
 *
 * @see \app\models\StatusMessage
 */
class StatusMessage extends ActiveQuery
{
    /**
     * @param $messageId
     * @param $userId
     * @return \app\models\StatusMessage|array|null
     */
    public function byMessageAndUser($messageId, $userId)
    {
        return $this->where(['messageId' => $messageId, 'userId' => $userId])->one();
    }

    /**
     * @inheritdoc
     * @return \app\models\StatusMessage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\StatusMessage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}