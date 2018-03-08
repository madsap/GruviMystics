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
use app\components\widgets\ReportUserAlert;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<!-- MARK.RELEASE:release.api_updates--20180307 -->
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!--
    <link href="css/default.css" rel="stylesheet" type="text/css" />-->
    <script src="<?= Url::to(["/css/bootstrap/js/bootstrap.min.js"], true);?>" type="text/javascript"></script>
    <link href="<?= Url::to(["/css/bootstrap/css/bootstrap.min.css"], true);?>" rel="stylesheet" type="text/css" />
    
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
    <style>
        .text-indent{
            text-indent: 30px;
        }
        .text-indent-2{
            text-indent: 60px;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>
    
<div class="wrap">
    <header>
        <div class="container" style="position:relative;">
            <nav class="navbar navbar-default">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="<?= Url::to(["/"], true);?>"><img src="<?= Url::to(['/images/logo.png'], true);?>" alt="" class="img-responsive"/></a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                          <span class="sr-only">Toggle navigation</span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                        </button>
                      </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav navbar-right">
                            
                                    
                        <?php if(User::isAdmin()){ ?>
                            <li><a href="<?= Url::to(['/user/readers'], true);?>" class="btn">Activity</a></li>
                        <?php }else{ ?>
                            <li><a href="<?= Url::to(['/site/about'], true);?>">about</a></li>
                        <?php } ?>  
                        <?php if(User::isUser()){ ?>
                            <li><a href="<?= Url::to(['/gruvi-bucks/add'], true);?>">add GruviBucks</a></li>
                        <?php } ?>  
                        
                          <?php if(Yii::$app->user->isGuest){ ?>
                                    <li><a href="<?= Url::to(['/user/sign-up'], true);?>" class="btn">Signup</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="btn" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Login</a>
                                        <div class="dropdown-menu" aria-labelledby="dLabel">
                                            <a href="<?= Url::to(['/site/auth?authclient=facebook'], true); ?>" class="login_with_fb">
                                                <img src="<?= Url::to(['/images/facebook_logo.png'], true);?>" alt=""/>
                                                <span class="text-bold">Login with<br/>Facebook</span>
                                            </a>
                                            <div class="text-pink text-center h4">or email</div>
                                            
                                            <?php $form = ActiveForm::begin([
                                                'id' => 'login-form',
                                                'action' => Url::to(['/site/login']),
                                                'options' => ['class' => 'form-custom']                                                
                                            ]); ?>
                                            
                                            <div class="form-group">
                                                <input required name="LoginForm[email]" type="text" class="form-control" placeholder="EMAIL" />
                                            </div>
                                            <div class="form-group">
                                                <input required name="LoginForm[password]" type="password" class="form-control" placeholder="PASSWORD" />
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-block text-default text-bold btn_gruvi">LOGIN</button>
                                            </div>
                                            
                                            <?php ActiveForm::end(); ?>
                                            
                                            
                                        </div>
                                    </li>
                          <?php }else{ ?>
                                    <?php if(User::isReader() || User::isAdmin()){ ?>
                                            <li><a href="<?= Url::to(['/user-relation'], true);?>" class="btn">Blocked Users</a></li>
                                    <?php } ?>
                                    <?php if(User::isAdmin()){ ?>
                                            <li><a href="<?= Url::to(['/message'], true);?>" class="btn">Messages</a></li>
                                    <?php } ?>
                                    <?php $profile_url = (Yii::$app->user->identity->getAttribute('role') != User::ROLE_READER)?"/user/update":"/user/profile"; ?>
                                    <?php /*<li><a href="<?= Url::to(['/request-a-call'], true);?>" class="btn">Call Requests</a></li>*/ ?>
                                    <li><a href="<?= Url::to([$profile_url], true);?>" class="btn"><?= Html::encode(Yii::$app->user->identity->firstName.' '.Yii::$app->user->identity->lastName); ?></a></li>
                                    <li><a href="<?= Url::to(['/user/logout'], true);?>" class="btn">Logout</a></li>
                          <?php } ?>
                        </ul>
                        <div class="text-right griviBucks_text text-default">
                            <?php if(User::isUser()){ ?>
                                you have <span class="text-blue" id="gruvi_bucks_text_blue">$<?= Yii::$app->user->identity->getGruviBucksAmount(); ?></span> <span class="text-orange text-bold">GruviBucks</span>
                            <?php } ?>
                        </div>
                    </div>
                    
                
            </nav>
            <?php if(User::isAdmin()){ ?>
                        <div class="bg-orange text-default text-bold clearfix">
                            <h3 style="margin:10px 20px;">ADMIN</h3>
                        </div>
                    <?php } ?>
        </div>
       
   
        </header>
 <?php
//    NavBar::begin([
//        'brandLabel' => Yii::$app->name,
//        'brandUrl' => Yii::$app->homeUrl,
//        'options' => [
//            'class' => 'navbar-inverse navbar-fixed-top',
//        ],
//    ]);
//    echo Nav::widget([
//        'options' => ['class' => 'navbar-nav navbar-right'],
//        'items' => [
//            ['label' => 'About', 'url' => ['/site/about']],
//            Yii::$app->user->isGuest ? (
//                ['label' => 'Login', 'url' => ['/user/login']]
//            ) : (
//                '<li>'
//                . Html::beginForm(['/site/logout'], 'post')
//                . Html::submitButton(
//                    'Logout (' . Yii::$app->user->identity->username . ')',
//                    ['class' => 'btn btn-link logout']
//                )
//                . Html::endForm()
//                . '</li>'
//            ),
//            Yii::$app->user->isGuest ? ['label' => 'Sign Up', 'url' => ['/user/sign-up']] : ''
//        ],
//    ]);
//    NavBar::end();
//    ?>
    
    
    <div class="container">
        
 <?= Breadcrumbs::widget([
//            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">
            <a class="navbar-brand" href="#"><img src="<?= Url::to(['/images/logo.png'], true);?>" alt="" class="img-responsive"/></a>
            &copy; <?= date('Y') ?>-<?= Yii::$app->name ?> 
        </p>

        <p class="pull-right">
            <?= Html::a('Privacy Policy', Url::to(['site/privacy-policy'])) ?>
             |
            <?= Html::a('Terms & Service', Url::to(['site/terms-and-service'])) ?>
        </p>
    </div>
</footer>
    
<?php 

    if(!Yii::$app->user->isGuest)echo ExpiredSessionAlert::widget();
    echo CallDetails::widget();
    echo AddGruviBucks::widget();
    
    if(User::isUser()){
        echo Twilio::widget(['user' => Yii::$app->user->identity]);
    }
    
    if (1 || User::isReader()) {
        echo BlockUserAlert::widget([]);
    }
    //echo ReportUserAlert::widget([]);

    if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAvailableForCalls()){
        //if(User::isReader())$this->registerJs("var activityStatusInterval = setInterval(getReaderCalls, 4096);");
        echo Twilio::widget(['user' => Yii::$app->user->identity]);
    }
    
    $this->registerJs("window.baseUrl = '".Url::to(['/'], true)."';");
    //$this->registerJs('$("#add-gruvi-bucks-modal").modal("toggle");');
    //$this->registerJs('$("#expired_session_modal_alert").modal("toggle");');
    

?>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'global_modal',
    'headerOptions' => ['id' => 'global_modal_header'],
    'size' => 'modal-md',
    'closeButton' => false
]);
?>
    <div id="supercontainer-modal_placeholder"></div>
<?php
yii\bootstrap\Modal::end();
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
<?php if ( (Yii::$app->user->identity->activity != User::ACTIVITY_DISABLED) && !YII_ENV_DEV  ) { 
?>
                        window.pingInterval = setInterval(function(){Gruvi.ping()}, 4096);
<?php } ?>
            }); 
        </script>
<?php } ?>    
    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
