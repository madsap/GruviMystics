<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\UserRelation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-relation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'senderId') ?>

    <?= $form->field($model, 'recipientId') ?>

    <?= $form->field($model, 'messageId') ?>

    <?= $form->field($model, 'action') ?>

    <?php // echo $form->field($model, 'createAt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
