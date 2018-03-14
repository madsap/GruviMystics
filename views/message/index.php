<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use \yii\helpers\Url;
use \app\models\User;
use \app\models\Message;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\Message */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-wrapper bg-default col-xs-12">

    <h3 class="text-pink" style="margin:20px 30px 0px 30px;">
        <?= Html::encode($this->title) ?>
        <!-- <a class="btn btn_gruvi pull-right" href="<?= Url::to(['create'], true);?>">ADD</a> -->
    </h3>
    
   <?php

        $senderToFliter = (!User::isAdmin())?Yii::$app->user->identity->id:0;
        $columns = [];
        $columns[] = 'id';
        if(User::isAdmin()){
            $columns[] = [
                    'attribute' => 'customerId',
                    'value'     => function ($data) {
                        return (!empty($data->customer->id)?'#'.$data->customer->id.' '.$data->customer->firstName.' '.$data->customer->lastName:"-");
                    },
                    'filter'=> Message::getCustomerForSelect(),
                ];
        }
        $columns[] = [
                'attribute' => 'readerId',
                'value'     => function ($data) {
                    return (!empty($data->reader->id)?'#'.$data->reader->id.' '.$data->reader->firstName.' '.$data->reader->lastName:"-");
                },
                'filter'=>Message::getReaderForSelect($senderToFliter),
            ];
        $columns[] = 'message:ntext';
        $columns[] = [
                'attribute' => 'status',
                'filter'=>Message::$arrayStatuses,
            ];
        $columns[] = [
            'attribute' => 'createAt',
            'label'=>'Date',
        ];
        $columns[] = ['class' => 'yii\grid\ActionColumn']

    ?>
    
    <div class="page-container">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $columns,
            ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
