<?php
use yii\helpers\Html;
use \app\models\User;
use \app\models\Message;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$reporter = User::find()->where(['id' => $model->senderId])->one();
$reported = User::find()->where(['id' => $model->recipientId])->one();
$message =  Message::find()->where(['id' => $model->messageId])->one();
?>

<div class="template-email">
    This email is sent to inform that a user or reader has been reported by another member.

    <h5>Reporter:</h5>
    <ul>
        <li>Username: <?= $reporter->username ?></li>
        <li>Full Name: <?= $reporter->renderFullname() ?></li>
        <li>Email : <?= $reporter->email ?></li>
    </ul>
    
    <h5>Reported:</h5>
    <ul>
        <li>Username: <?= $reported->username ?></li>
        <li>Full Name: <?= $reported->renderFullname() ?></li>
        <li>Email : <?= $reported->email ?></li>
    </ul>

    <p> Reason : <?= $model->notes ?> </p>
    
    <p> Message : <?= $message->message ?> </p>

</div>
