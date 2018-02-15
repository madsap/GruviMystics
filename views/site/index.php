<?php

/**
 * @var \yii\web\View $this
 */

use app\components\widgets\ReadersTeaser;
use \app\models\User;
use \yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Main';
?>
<div class="gruvi-banner col-xs-12">
    <div class="row">
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img src="images/gm-Logo.png" alt=""></a>
            </div>
            <div class="collapse navbar-collapse gruvi-menu" id="myNavbar">
                <ul class="nav navbar-nav navbar-right">
                    <?php if (User::isAdmin()) { ?>
                        <li><a href="<?= Url::to(['/user/readers'], true); ?>" class="btn">Activity</a></li>
                    <?php } else { ?>
                        <li><a href="<?= Url::to(['/site/about'], true); ?>" class="btn">About</a></li>
                    <?php } ?>
                    <?php if (User::isUser()) { ?>
                        <li><a href="<?= Url::to(['/gruvi-bucks/add'], true); ?>">Add GruviBucks</a></li>
                    <?php } ?>
                    <?php if (Yii::$app->user->isGuest) { ?>
                        <li><a href="<?= Url::to(['/user/sign-up'], true); ?>" class="btn">Signup</a></li>
                        <li class="dropdown">
                            <a href="#" class="btn" id="dLabel" type="button" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">Login</a>
                            <div class="dropdown-menu" aria-labelledby="dLabel">
                                <a href="<?= Url::to(['/site/auth?authclient=facebook'], true); ?>"
                                   class="login_with_fb">
                                    <img src="<?= Url::to(['/images/facebook_logo.png'], true); ?>" alt=""/>
                                    <span class="text-bold">Login with<br/>Facebook</span>
                                </a>
                                <div class="text-pink text-center h4">or email</div>

                                <?php $form = ActiveForm::begin([
                                    'id' => 'login-form',
                                    'action' => Url::to(['/site/login']),
                                    'options' => ['class' => 'form-custom']
                                ]); ?>

                                <div class="form-group">
                                    <input required name="LoginForm[email]" type="text" class="form-control"
                                           placeholder="EMAIL"/>
                                </div>
                                <div class="form-group">
                                    <input required name="LoginForm[password]" type="password" class="form-control"
                                           placeholder="PASSWORD"/>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-block text-default text-bold btn_gruvi">LOGIN
                                    </button>
                                </div>

                                <?php ActiveForm::end(); ?>


                            </div>
                        </li>
                    <?php } else { ?>
                        <?php if (User::isReader() || User::isAdmin()) { ?>
                            <li><a href="<?= Url::to(['/user-relation'], true); ?>" class="btn">Blocked Users</a></li>
                        <?php } ?>
                        <?php if (User::isAdmin()) { ?>
                            <li><a href="<?= Url::to(['/message'], true); ?>" class="btn">Messages</a></li>
                        <?php } ?>
                        <?php $profile_url = (Yii::$app->user->identity->getAttribute('role') != User::ROLE_READER) ? "/user/update" : "/user/profile"; ?>
                        <?php /*<li><a href="<?= Url::to(['/request-a-call'], true);?>" class="btn">Call Requests</a></li>*/ ?>
                        <li><a href="<?= Url::to([$profile_url], true); ?>"
                               class="btn"><?= Html::encode(Yii::$app->user->identity->firstName . ' ' . Yii::$app->user->identity->lastName); ?></a>
                        </li>
                        <li><a href="<?= Url::to(['/user/logout'], true); ?>" class="btn">Logout</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    </div>
    <div class="row">
    <div class="register-header">
        <div class="register-content">
            <img class="tagline" src="images/register_tagline@2x.png" alt="">
            <a href="<?= Url::to(['/user/sign-up'], true); ?>" class="btn-lg btn-primary gruvi-btn" role="button">Register</a>
        </div>
        <!--            <div class="register-content">Receive <div class="count"><span>10</span></div><img class="free-image" srcset="images/free.png 1x, images/free@2x.png 2x"/>GruviBucks when you</div>-->
    </div>
    <div class="tagline">Authentic Accurate Available Light workers</div>
    </div>
</div>
<div id="readers_teaser_cnt col-xs-12"><?= ReadersTeaser::widget(); ?></div>

<div class="home_bottom text-default col-xs-12">
    <h3 class="text-bold">Why Gruvi Mystics?</h3>
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            Why pick Gruvi Mystics? <br/>
            We screen all of our readers and psychics through actual sessions and our bar is high.
            Bottomline, we only work with the best readers, psychics and mediums.
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 text-center">
                    <img src="../images/foto.jpg" class=" img-thumbnail"/>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12 text-center">
                    <img src="../images/foto.jpg" class=" img-thumbnail"/>
                </div>
            </div>
        </div>
    </div>
</div>