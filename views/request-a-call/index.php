<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\RequestACall */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-wrapper request-acall-index col-xs-12">
    <div class="page-title text-default h3"><?= Html::encode($this->title) ?></div>
    <div class="page-container">


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /*Html::a('Create Request Acall', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'customerId',
            'readerId',
            'phone',
            'status',
            'createAt',
        ],
    ]); ?>
     <!-- ['class' => 'yii\grid\ActionColumn'], -->
     
     </div>
</div>
