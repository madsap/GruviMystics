<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = 'Update Message: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="page-wrapper bg-default col-xs-12">

    <div class="page-title text-default h3"><?= Html::encode($this->title) ?></div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
