<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\models\User;
use \yii\helpers\Url;


$this->title = 'Reader Details';
?>
<div class="page-wrapper tag-views_user_reader col-xs-12">
    <?php if (Yii::$app->session->hasFlash('success')) { ?>
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
    <?php } ?>
<?php $sessionUser = Yii::$app->user; ?>
    <?=$this->render('../_nav.php')?>;
   <div class="page-title text-default h3">ADMIN : <?=$this->title?></div>

    <div class="page-container panel panel-default">

        <section class="row">
            <article class="col-sm-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><?= Html::a('Info','#tab-basicInfo', ['role'=>'tab','data-toggle'=>'tab']) ?></li>
                    <li><?= Html::a('Calls','#tab-calls', ['role'=>'tab','data-toggle'=>'tab']) ?></li>
                    <li><?= Html::a('Messages','#tab-messages', ['role'=>'tab','data-toggle'=>'tab']) ?></li>
                </ul>
            </article>
        </section>
        <div class="panel-body">

            <div class="row">
                <div class="tab-content col-sm-12">

                    <div role="tabpanel" class="tab-pane active" id="tab-basicInfo">
                        <table class="table">
                            <tr>
                                <td>ID</td>
                                <td><?= $u->id ?></td>
                            </tr>
                            <tr>
                                <td>Role:</td>
                                <td><?= $u->role ?></td>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td><?= $u->email ?></td>
                            </tr>
                            <tr>
                                <td>First Name:</td>
                                <td><?= $u->firstName ?></td>
                            </tr>
                            <tr>
                                <td>Last Name:</td>
                                <td><?= $u->lastName ?></td>
                            </tr>
                            <tr>
                                <td>Display Name:</td>
                                <td><?= $u->displayname ?></td>
                            </tr>
                            <tr>
                                <td>User Name:</td>
                                <td><?= $u->username ?></td>
                            </tr>
                            <tr>
                                <td>Date:</td>
                                <td><?= $u->createAt ?></td>
                            </tr>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="tab-calls">
                        <table class="table">
                            <tr>
                                <th>ID</th>
                                <th>From Customer</th>
                                <th>To Reader</th>
                                <th>Duration</th>
                                <th>Date</th>
                            </tr>
                            <?php foreach ($calls as $c) { ?>
                            <tr>
                                <th><?= Html::a($c->id, Url::toRoute(['admin/call/show','pkid'=>$c->id])) ?></th>
                                <td><?= $c->customer->renderFullname() ?></td>
                                <td><?= $c->reader->renderFullname() ?></td>
                                <td><?= $c->duration ?></td>
                                <td><?= $c->createAt ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="tab-messages">
                        <table class="table">
                            <tr>
                                <th>ID</th>
                                <th>From Customer</th>
                                <th>To Reader</th>
                                <th>status</th>
                                <th>Date</th>
                            </tr>
                            <?php foreach ($u->messagesReaders as $m) { ?>
                            <tr>
                                <th><?= Html::a($m->id, Url::toRoute(['admin/message/show','pkid'=>$m->id])) ?></th>
                                <td><?= $m->customer->renderFullname() ?></td>
                                <td><?= $m->reader->renderFullname() ?></td>
                                <td><?= $m->status ?></td>
                                <td><?= $m->createAt ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>

                </div>  <!-- col -->
            </div> <!-- row -->

        </div> <!-- panel-body -->

    </div> <!-- page-container -->

</div> <!-- page-wrapper -->
