<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Update User: ' . (!empty(trim($model->firstName)) || !empty(trim($model->lastName)))?$model->firstName.' '.$model->lastName:$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="page-wrapper tag-views.user.update">
    <div class="page-title text-default h3"><?= Html::encode($this->title) ?></div>
    <div class="page-container panel panel-default">
        <div class="user-update panel-body col-md-6 col-sm-10">

            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
