<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\RequestACall */

$this->title = 'Create Request Acall';
$this->params['breadcrumbs'][] = ['label' => 'Request Acalls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-acall-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
