<?php

namespace app\models\search;

use \Yii;
use \yii\base\Model;
use \yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use \app\models\User as UserModel;
use yii\db\Expression;
use yii\data\Pagination;
use yii\widgets\LinkPager;

/**
 * Class User
 * User represents the model behind the search form about `app\models\User`.
 * @package app\models\search
 * @author: Alexander Mohon`ko
 * Date: 06.07.17
 *
 * @property string  $emails
 * @property string  $keyword
 * @property integer $minId
 * @property integer $maxId
 * @property integer $limit
 */
class User extends UserModel
{
    public $emails;
    public $keyword;
    public $minId;
    public $maxId;
    public $limit = 128;
    public $activity;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['limit'], 'default', 'value' => 100],
            [['emails', 'keyword'], 'safe'],
            [['registrationType', 'email', 'username', 'status', 'createAt'], 'safe'],
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
        $query = UserModel::find()->alias('u')->select('u.*');

        $query->where('u.id <> :myId', [':myId' => $this->myId]);

        // grid filtering conditions
        $query->orFilterWhere([
            'u.id'                  => $this->id,
            'u.registrationType'    => $this->registrationType,
            'u.status'              => $this->status,
            'u.createAt'            => $this->createAt,
        ]);

        $query->orFilterWhere(['like', 'u.email', $this->keyword])
            ->orFilterWhere(['like', 'u.username', $this->keyword]);

        $sort = SORT_DESC;

        if(!empty($this->minId)) {
            $query->andWhere('u.id > :id', [':id' => $this->minId]);
            $sort = SORT_ASC;
        }
        else {
            if(!empty($this->maxId)) {
                $query->andWhere('u.id < :id', [':id' => $this->maxId]);
            }
        }

        $query->orderBy(['u.createAt' => $sort]);

        $query->limit($this->limit);

        return $this->getAllPublicAttributes($query->all());
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @return array
     */
    public function getReaders()
    {
        $query = UserModel::find()
                ->joinWith(['callsReaders c' => function($query) {
                                    $query->joinWith('customer');
                                }], true, 'LEFT JOIN')
                ->alias('u')->select('u.*');

        $query->where('u.role = :role', [':role' => User::ROLE_READER]);

        $query->orderBy(['u.createAt' => SORT_DESC]);

        $query->limit($this->limit);

        return $query->all();
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @return array
     */
    public function getTeaserReaders($page, $limit, $filter = [])
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT `activity`, COUNT(*) as c FROM `md_user` WHERE `role` = 'reader' GROUP BY `activity`");
        $result = $command->queryAll();

        $total_count = 0;
        $available = 0;
        foreach($result as $row){
            $total_count += $row['c'];
            if($row['activity'] == User::ACTIVITY_ONLINE)$available = $row['c'];
        }
        
        $query = UserModel::find()->alias('u')->select('u.*');
        $query->where('u.role = :role', [':role' => User::ROLE_READER]);
        if(!empty($filter['activity']))$query->andWhere('u.activity = :activity', [':activity' => $filter['activity']]);
        if(!empty($filter['keyword'])){
            $query->andWhere(['or',
            ['like','LOWER(u.firstName)', strtolower($filter['keyword'])],
            ['like','LOWER(u.lastName)', strtolower($filter['keyword'])]]);
        }
        $query->orderBy('u.`activity` DESC, u.`activity_update` DESC');//new Expression('rand()')
        $query->offset($page*$limit);
        $query->limit($limit);
        
        $count_to_page = ($filter['activity'] != User::ACTIVITY_ONLINE)?$total_count:$available;
        
        $pages = new Pagination(['totalCount' => $count_to_page, 'pageSize' => $limit, 'page' => $page]);
                
                
        $aRet = [];
        $aRet['available'] = $available;
        $aRet['total_count'] = $total_count;
        $aRet['pages'] = $pages;
        $aRet['readers'] = $query->all();
        
        return $aRet;
    }
    
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchDP($params)
    {
        $query = UserModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        
        if(!empty($params['User']['role']))$this->role = $params['User']['role'];

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'createAt' => $this->createAt,
        ]);

        //echo $this->role;exit;
        $query->andFilterWhere(['like', 'role', $this->role])
            ->andFilterWhere(['like', 'registrationType', $this->registrationType])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}