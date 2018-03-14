<?php
/**
 * @author Alexander Mokhonko
 * Date: 06.07.17
 *
 * @var \yii\web\View               $this
 * @var \yii\bootstrap\ActiveForm   $form
 * @var \app\models\User           $user
 */

use \yii\helpers\Html;
use \yii\bootstrap\ActiveForm;
use \kartik\date\DatePicker;
use \yii\helpers\Url;

$this->title = 'Start new ACCOUNT';
?>
<div class="page-wrapper col-xs-12">
    <div class="page-title text-default h3"><?= Html::encode($this->title) ?></div>
    <div class="page-container panel panel-default signup_page">
        <h4 class="text-uppercase text-pink" style="margin-top:0px;">Create an account</h4>
        <p>In order to place you on a call with our psychics, you need to first create a private and confidential account.</p>
        
        <a href="<?= Url::to(['/site/auth?authclient=facebook'], true); ?>" class="login_with_fb">
            <img src="<?= Url::to(['/images/facebook_logo.png'], true);?>" alt=""/>
            <span class="text-bold">Login with Facebook</span>
        </a>
        <div class="text-center h5">OR</div>
        <div class="text-pink text-center h4">Sign up with email</div>
        
        <?php $form = ActiveForm::begin([
            'id' => 'sign-up-anonymous-form',
            'options' => ['class' => ''],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'control-label'],
            ],
        ]); ?>

        <?php /*var_dump($user->getErrors())*/ ?>

        <?php /* print_r($user);exit;*/ ?>
        <div class="form-container">
        <?= $form->field($user, 'firstName')
            ->textInput(['autofocus' => true, 'placeholder' => $user->getAttributeLabel('firstName')])
            ->label(false)
        ?>

        <?= $form->field($user, 'lastName')
            ->textInput(['autofocus' => true, 'placeholder' => $user->getAttributeLabel('lastName')])
            ->label(false)
        ?>

        <?= $form->field($user, 'email')
            ->textInput(['autofocus' => true, 'placeholder' => $user->getAttributeLabel('email')])
            ->label(false)
        ?>

        <?= $form->field($user, 'password')
            ->passwordInput(['autofocus' => true, 'placeholder' => $user->getAttributeLabel('password')])
            ->label(false)
        ?>

        <?= $form->field($user, 'confirmPassword')
            ->passwordInput(['autofocus' => true, 'placeholder' => $user->getAttributeLabel('confirmPassword')])
            ->label(false)
        ?>

        <?= $form->field($user, 'dob')
            ->widget(
                    DatePicker::classname(),
                    [
                        'name' => 'dob',
                        'type' => DatePicker::TYPE_INPUT,
                        'options' => ['value' => $user->dob, 'placeholder' => $user->getAttributeLabel('dob')],
                        'pluginOptions' => [
                            'format' => 'dd-M-yyyy',
                            'todayHighlight' => true
                        ]
                    ]
            )
            ->label(false)
        ?>
        </div>
        <div class="form-group">
            <div class="">
                <?php echo Html::submitButton('CREATE ACCOUNT', ['class' => 'btn btn-block text-default btn_gruvi', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
        Already a customer? 
        <br>
        <a href="<?= Url::to(['/site/login'], true);?>">Click here to LOGIN</a>
    </div>
    
</div>