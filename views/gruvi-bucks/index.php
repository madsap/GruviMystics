<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\GruviBucks */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gruvi Bucks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gruvi-bucks-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Gruvi Bucks', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'userId',
            'creditCardId',
            'stripeTransaction',
            'amount',
            // 'log:ntext',
            // 'status',
            // 'createAt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
