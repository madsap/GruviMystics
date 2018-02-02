<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RequestACall */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-acall-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customerId')->textInput() ?>

    <?= $form->field($model, 'readerId')->textInput() ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'New' => 'New', 'Accepted' => 'Accepted', 'Declined' => 'Declined', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'createAt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
