<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="page-container" style='width:640px;'>

    <?php $form = ActiveForm::begin(); ?>

    <div style='display: none'>
        <?= $form->field($model, 'customerId')->textInput() ?>
        <?= $form->field($model, 'readerId')->textInput() ?>
    </div>
        
    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'visible' => 'Visible', 'deleted' => 'Deleted', 'banned' => 'Banned', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'createAt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
