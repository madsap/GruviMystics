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
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!--
        <link href="css/default.css" rel="stylesheet" type="text/css" />-->
        <script src="<?= Url::to(["/css/bootstrap/js/bootstrap.min.js"], true); ?>" type="text/javascript"></script>
        <link href="<?= Url::to(["/css/bootstrap/css/bootstrap.min.css"], true); ?>" rel="stylesheet" type="text/css" />

        <?= Html::csrfMetaTags() ?>
        <title>Terms & Conditions</title>
        <?php $this->head() ?>
        <style>
            .text-indent{
                text-indent: 30px;
            }
            .text-indent-2{
                text-indent: 60px;
            }
           body#terms .page-container {
                padding: 40px 50px;
           }
        </style>
    </head>

    <body id="terms">
        <?php $this->beginBody() ?>

        <div class="wrap">
            <header>
                <div class="container" style="position:relative;">
                    <nav class="navbar navbar-default">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="javascript:void(0);"><img src="<?= Url::to(['/images/logo.png'], true); ?>" alt="" class="img-responsive"/></a>
                        </div>
                    </nav>
                </div>
            </header>

            <?php $this->title = 'Terms & Conditions'; ?>

            <div class="page-wrapper">

                <div class="page-title text-default h3"><?= Html::encode($this->title) ?></div>
                <div class="page-container panel panel-default">
<?php
include('_termsContent.php');
?>
                </div>

            </div><!-- page-wrapper -->
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">
                <a class="navbar-brand" href="#"><img src="<?= Url::to(['/images/logo.png'], true); ?>" alt="" class="img-responsive"/></a>
                &copy; <?= date('Y') ?>-<?= Yii::$app->name ?> 
            </p>

            <p class="pull-right">
                <?= Html::a('Privacy Policy', Url::to(['site/privacy-policy'])) ?>
                |
                <?= Html::a('Terms & Service', Url::to(['site/terms-and-service'])) ?>
            </p>
        </div>
    </footer>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
