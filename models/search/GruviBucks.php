<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\GruviBucks as GruviBucksModel;

/**
 * GruviBucks represents the model behind the search form about `app\models\GruviBucks`.
 */
class GruviBucks extends GruviBucksModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userId', 'creditCardId'], 'integer'],
            [['stripeTransaction', 'log', 'status', 'createAt'], 'safe'],
            [['amount'], 'number'],
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
        $query = GruviBucksModel::find();

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
            'userId' => $this->userId,
            'creditCardId' => $this->creditCardId,
            'amount' => $this->amount,
            'createAt' => $this->createAt,
        ]);

        $query->andFilterWhere(['like', 'stripeTransaction', $this->stripeTransaction])
            ->andFilterWhere(['like', 'log', $this->log])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
