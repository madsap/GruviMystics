<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php /*$form->field($model, 'role')->dropDownList([ 'super_admin' => 'Super admin', 'bar_admin' => 'Bar admin', 'user' => 'User', ], ['prompt' => ''])*/ ?>

    <?php /*$form->field($model, 'registrationType')->dropDownList([ 'email' => 'Email', ], ['prompt' => ''])*/ ?>
	
	<?= $form->field($model, 'firstName')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'lastName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
	
	<input type="password" style="display:none"><!-- disables autocomplete -->
	<?= $form->field($model, 'password')->passwordInput(['autocomplete' => "off"]) ?>
	<?= $form->field($model, 'confirmPassword')->passwordInput(['autocomplete' => "off"]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
