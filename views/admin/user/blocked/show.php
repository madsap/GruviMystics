<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\models\User;
use \yii\helpers\Url;


$this->title = 'Blocked User/Reader';
?>
<div class="page-wrapper tag-views_user_reader col-xs-12">
    <?php if (Yii::$app->session->hasFlash('success')) { ?>
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
    <?php } ?>
<?php 
$sessionUser = Yii::$app->user;
//var_dump($sessionUser->identity->role);
//die;
 ?>
    <div class="page-title text-default h3">ADMIN : Blocked User/Reader</div>

    <div class="page-container panel panel-default">

        <div class="panel-body">

            <div class="row">
                <div class="col-sm-12">

                    <table class="table">
                        <tr>
                            <td>ID</td>
                            <td><?= $b->id ?></td>
                        </tr>
                        <tr>
                            <td>Blocker (Sender) Email:</td>
                            <td><?= $b->sender->email ?></td>
                        </tr>
                        <tr>
                            <td>Blocker (Sender) Username:</td>
                            <td><?= $b->sender->username ?></td>
                        </tr>
                        <tr>
                            <td>Blocked (Recipient) Email:</td>
                            <td><?= $b->recipient->email ?></td>
                        </tr>
                        <tr>
                            <td>Blocked (Recipient) Username:</td>
                            <td><?= $b->recipient->username ?></td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td><?= $b->createAt ?></td>
                        </tr>
                    </table>

                </div>  <!-- col -->
            </div> <!-- row -->

        </div> <!-- panel-body -->

    </div> <!-- page-container -->

</div> <!-- page-wrapper -->
