<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-wrapper site-login">
    <div class="page-title text-default h3"><?= Html::encode($this->title) ?></div>
    <div class="page-container panel panel-default">
        <div class="panel-body text-violet">
            
        


    
    <?php if(Yii::$app->session->hasFlash('facebookError')){ ?>
        <div style="font-size:16px;color:red;background-color: white;"><?= Yii::$app->session->getFlash('facebookError'); ?></div>
    <?php } ?>
    <div class="page-container">    
        
            <a href="<?= Url::to(['/site/auth?authclient=facebook'], true); ?>" class="login_with_fb">
                <img src="<?= Url::to(['/images/facebook_logo.png'], true); ?>" alt="" style="width:32px;">
                <span class="text-bold">Login with Facebook</span>
            </a>


        <p>Please fill out the following fields to login:</p>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ]) ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
        
        <a href="<?= Url::to(['/site/request-password-reset'], true); ?>">Forgot Your Password?</a>
        
        </div>
    </div>

</div>
