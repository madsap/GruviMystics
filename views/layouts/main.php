<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use \yii\helpers\Url;
use app\assets\AppAsset;
use \app\models\User;
use app\components\widgets\CallDetails;
use app\components\widgets\Twilio;
use app\components\widgets\AddGruviBucks;
use app\components\widgets\ExpiredSessionAlert;
use app\components\widgets\BlockUserAlert;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<!-- MARK RELEASE 20180209-a -->
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!--
    <link href="css/default.css" rel="stylesheet" type="text/css" />-->
<!--    <script src="--><?//= Url::to(["/css/bootstrap/js/bootstrap.min.js"], true);?><!--" type="text/javascript"></script>-->
<!--    <link href="--><?//= Url::to(["/css/bootstrap/css/bootstrap.min.css"], true);?><!--" rel="stylesheet" type="text/css" />-->
    
<!--    <link href="<?= Url::to(["css/modal.css"], true);?>" rel="stylesheet" type="text/css" />-->
    <?= Html::csrfMetaTags() ?>
    <?php 
        $this->registerJsFile('@web/js/gruvi.js');  
        $this->registerJsFile('@web/js/common.js');  
        if(User::isReader() || User::isAdmin())$this->registerJsFile('@web/js/reader.js');  
        if(User::isUser() || User::isAdmin())$this->registerJsFile('@web/js/customer.js');  
    ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
<!--    <style>-->
<!--        .text-indent{-->
<!--            text-indent: 30px;-->
<!--        }-->
<!--        .text-indent-2{-->
<!--            text-indent: 60px;-->
<!--        }-->
<!--    </style>-->
</head>
<body>
<?php $this->beginBody() ?>
    
<div class="wrap">

    <div class="container">
        
 <?= Breadcrumbs::widget([
//            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
    
<?php 

    if(!Yii::$app->user->isGuest)echo ExpiredSessionAlert::widget();
    echo CallDetails::widget();
    echo AddGruviBucks::widget();
    
    if(User::isUser()){
        echo Twilio::widget(['user' => Yii::$app->user->identity]);
    }
    
    if(User::isReader()){
        echo BlockUserAlert::widget([]);
    }

    if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAvailableForCalls()){
        //if(User::isReader())$this->registerJs("var activityStatusInterval = setInterval(getReaderCalls, 4096);");
        echo Twilio::widget(['user' => Yii::$app->user->identity]);
    }
    
    $this->registerJs("window.baseUrl = '".Url::to(['/'], true)."';");
    //$this->registerJs('$("#add-gruvi-bucks-modal").modal("toggle");');
    //$this->registerJs('$("#expired_session_modal_alert").modal("toggle");');
    

?>
    
<?php if(!Yii::$app->user->isGuest){ ?>
        <script>
            $( document ).ready(function() {
                    window.userRole = '<?= Yii::$app->user->identity->getAttribute('role'); ?>';
                    window.controller = '<?= Yii::$app->controller->id; ?>';
                    window.action = '<?= Yii::$app->controller->action->id; ?>';
                    <?php if(!empty($this->params['readerAjaxUpdate'])){ ?> 
                        window.readerAjaxUpdate = '<?= $this->params['readerAjaxUpdate']; ?>';
                    <?php } ?>
<?php 
if ( (Yii::$app->user->identity->activity != User::ACTIVITY_DISABLED) && !YII_ENV_DEV  )
{ 
?>
                        window.pingInterval = setInterval(function(){Gruvi.ping()}, 4096);
<?php 
}
?>
            }); 
        </script>
<?php } ?>    
    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
