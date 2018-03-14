<?php

use yii\helpers\Html;
use \app\models\User;
use \app\models\UserRelation;
use yii\grid\GridView;
use \yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\UserRelation */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Relations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-wrapper bg-default col-xs-12">
    <h3 class="text-pink" style="margin:20px 30px 0px 30px;">
        a List of Blocked/Reported Users
        <!-- <a class="btn btn_gruvi pull-right" href="<?= Url::to(['/user-relation/create'], true);?>">ADD</a> -->
    </h3>
    <div class="page-container">

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        
            <?php

                $senderToFliter = (!User::isAdmin())?Yii::$app->user->identity->id:0;
                $columns = [];
                $columns[] = 'id';
                if(User::isAdmin()){
                    $columns[] = [
                            'attribute' => 'senderId',
                            'value'     => function ($data) {
                                return (!empty($data->sender->id)?'#'.$data->sender->id.' '.$data->sender->firstName.' '.$data->sender->lastName:"-");
                            },
                            'filter'=> UserRelation::getSenderForSelect(),
                        ];
                }
                $columns[] = [
                        'attribute' => 'recipientId',
                        'value'     => function ($data) {
                            return (!empty($data->recipient->id)?'#'.$data->recipient->id.' '.$data->recipient->firstName.' '.$data->recipient->lastName:"-");
                        },
                        'filter'=>UserRelation::getRecepientForSelect($senderToFliter),
                    ];
                $columns[] = [
                    'attribute' => 'action',
                    'label' => 'Action',
                    //'value' => 'message.message'
                    ];
                $columns[] = [
                    'attribute' => 'messageText',
                    'label' => 'Message Text',
                    'value' => 'message.message'
                    ];
                
                $columns[] = [
                    'attribute' => 'createAt',
                    'label'=>'Date',
                ];
                $columns[] = [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}{unblock}',
                        'buttons' => array(
                            'unblock' => function($url, $model, $key) {     // render your custom button
                                    return '<a href="#" onclick="unblockUser('.$model->id.'); return false;" title="unblock" aria-label="unblock"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></a>';
                                },
                        )
                    ];

            ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $columns,
            ]); ?>
        
    </div>
</div>
