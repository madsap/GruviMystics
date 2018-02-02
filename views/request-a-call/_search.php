<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\RequestACall */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-acall-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'customerId') ?>

    <?= $form->field($model, 'readerId') ?>

    <?= $form->field($model, 'phone') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'createAt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
