<?php

namespace app\models\search;

use \yii\base\Model;
use \yii\db\ActiveQuery;
use \app\models\Chat as ChatModel;

/**
 * Chat represents the model behind the search form about `\app\models\Chat`.
 *
 * @property integer $userId
 * @property integer $minId
 * @property integer $maxId
 * @property integer $limit
 */
class Chat extends ChatModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'countParticipants', 'myId'], 'integer'],
            [['cHash', 'lastMessageTime', 'status', 'createAt'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @return array
     */
    public function search()
    {
        $userId = $this->myId;

        $query = ChatModel::find()->alias('t');

        $query->joinWith(
            ['participantChats' => function($subQuery) use ($userId) {
                /** @var ActiveQuery $subQuery */
                return $subQuery->andOnCondition([
                    'pc2.userId' => $userId,
                ]);
            }],
            true,
            'JOIN'
        );

        $query->joinWith(['newMessage' => function($subQuery) use ($userId) {
            /** @var ActiveQuery $subQuery */
            return $subQuery->andOnCondition(['sm.userId' => $userId]);
        }]);

        // grid filtering conditions
        $query->andFilterWhere([
            'cHash' => $this->cHash,
            'countParticipants' => $this->countParticipants,
        ]);

        $query->andWhere('lastMessageTime <> "0000-00-00 00:00:00"');

        $query->orderBy(['t.lastMessageTime' => SORT_DESC]);

        return $this->getAllPublicAttributes($query->all());
    }
}