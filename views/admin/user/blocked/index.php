<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\models\User;
use \yii\helpers\Url;


$this->title = 'Blocked Users/Readers';
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
    <div class="page-title text-default h3">ADMIN : Blocked Users/Readers</div>

    <div class="page-container panel panel-default">

        <div class="panel-body">

            <div class="row">
                <div class="col-sm-12">

                    <table class="table">
                        <tr>
                            <th>ID</th>
                            <th>Blocker (Sender)</th>
                            <th>Blockee (Receiver)</th>
                            <th>Date</th>
                        </tr>
                        <?php foreach ($blocked as $b) { ?>
                        <tr>
                            <th><?= Html::a($b->id, Url::toRoute(['admin/users/blocked','pkid'=>$b->id])) ?></th>
                            <td><?= $b->sender->email ?></td>
                            <td><?= $b->recipient->email ?></td>
                            <td><?= $b->createAt ?></td>
                        </tr>
                        <?php } ?>
                    </table>

                </div>  <!-- col -->
            </div> <!-- row -->

        </div> <!-- panel-body -->

    </div> <!-- page-container -->

</div> <!-- page-wrapper -->
