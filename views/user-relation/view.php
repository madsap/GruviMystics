<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UserRelation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-wrapper bg-default">
    <h3 class="text-pink" style="margin:20px 30px 0px 30px;">
        <?= Html::encode($this->title) ?>
    </h3>
    <div class="page-container">

        <p>
            <?php if(!empty(Yii::$app->request->referrer))echo '<a class="btn btn-primary" href="'.Yii::$app->request->referrer.'">Back</a>'; ?>
            <?php /*Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])*/ ?>
            <?php /*Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])*/ ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'senderId',
                'sender.firstName',
                'sender.lastName',
                'sender.email',
                'recipientId',
                'recipient.firstName',
                'recipient.lastName',
                'recipient.email',
                'messageId',
                'message.message',
                'action',
                'notes',
                'createAt',
            ],
        ]) ?>

    </div>
</div>
