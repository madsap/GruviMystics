<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\models\User;
use \yii\helpers\Url;

$this->title = 'All Readers';
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
    <?=$this->render('../_nav.php')?>;
    <div class="page-title text-default h3">ADMIN : All Readers</div>

    <div class="page-container panel panel-default">

        <div class="panel-body">

            <div class="row">
                <div class="col-sm-12">

                    <table class="table">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Displayname</th>
                            <th>Email</th>
                            <th>Date</th>
                        </tr>
                        <?php foreach ($records as $r) { ?>
                        <tr>
                            <th><?= Html::a($r->id, Url::toRoute(['admin/user/show','pkid'=>$r->id])) ?></th>
                            <td><?= $r->username ?></td>
                            <td><?= $r->displayname ?></td>
                            <td><?= $r->email ?></td>
                            <td><?= $r->createAt ?></td>
                        </tr>
                        <?php } ?>
                    </table>

                </div>  <!-- col -->
            </div> <!-- row -->

        </div> <!-- panel-body -->

    </div> <!-- page-container -->

</div> <!-- page-wrapper -->
