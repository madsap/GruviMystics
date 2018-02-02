<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Message as MessageModel;

/**
 * Message represents the model behind the search form about `app\models\Message`.
 */
class Message extends MessageModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customerId', 'readerId'], 'integer'],
            [['message', 'status', 'createAt'], 'safe']
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
        $query = MessageModel::find();

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
            'customerId' => $this->customerId,
            'readerId' => $this->readerId,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'createAt', $this->createAt]);

        return $dataProvider;
    }
    

    /**
     * get bunch of chat messages
     *
     * @param int $minMessageId return messages where id > minMessageId
     * @param int $readerId where 'readerId' = $readerId
     * @param int $maxMessageId return messages where id < maxMessageId
     * @return array
     */
    public function chatList($readerId, $minMessageId = null, $maxMessageId = null, $limit = 50){
        
        $query = MessageModel::find()->alias("m")->joinWith('customer');
        $query->andWhere(['readerId' => $readerId, 'm.status' => Message::STATUS_VISIBLE]);
        if(!empty($minMessageId)){
            $query->andWhere("m.id > :minMessageId", ['minMessageId' => $minMessageId]);
        }
        if(!empty($maxMessageId)){
            $query->andWhere("m.id < :maxMessageId", ['maxMessageId' => $maxMessageId]);
        }
        $query->orderBy("m.id DESC");
        $query->limit = $limit;

        $data = $query->all();
        
        $data = array_reverse($data);//we need asc in view
        
        return $data;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param $this->id, $minMessageId, $myId
     *
     * @return ActiveDataProvider
     */
    public function clearList($readerId){
        
        $query = MessageModel::find()->select("GROUP_CONCAT(`id`) as ids")->asArray();
        $query->andWhere(['readerId' => $readerId]);
        $query->andWhere("`status` <> '".Message::STATUS_VISIBLE."'");
        $query->andWhere("`changeAt` > NOW() - INTERVAL 10 MINUTE");
        $query->limit = 512;
        //Array ( [ids] => 80,79 ) 
        return $query->one();
        
    }
    
}
