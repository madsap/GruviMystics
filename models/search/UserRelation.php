<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserRelation as UserRelationModel;

/**
 * UserRelation represents the model behind the search form about `app\models\UserRelation`.
 */
class UserRelation extends UserRelationModel
{
    public $messageText;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'senderId', 'recipientId', 'messageId'], 'integer'],
            [['action', 'createAt', 'messageText'], 'safe'],
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
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserRelationModel::find()->alias('ur')->joinWith('message');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'senderId' => $this->senderId,
            'recipientId' => $this->recipientId,
            'messageId' => $this->messageId
        ]);

        $query->andFilterWhere(['like', 'action', $this->action]);
        $query->andFilterWhere(['like', 'ur.createAt', $this->createAt]);
        $query->andFilterWhere(['like', 'md_message.message', $this->messageText]);

        return $dataProvider;
    }
}
