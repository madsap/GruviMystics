<?php
use \app\models\User;
use \app\models\Message;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$reporter = User::find()->where(['id' => $model->senderId])->one();
$reported = User::find()->where(['id' => $model->recipientId])->one();
$message =  Message::find()->where(['id' => $model->messageId])->one();
?>
This email is sent to inform that a user or reader has been reported by another member.

Reporter:

Username: <?= $reporter->username ?>
Full Name: <?= $reporter->renderFullname() ?>
Email : <?= $reporter->email ?>
    
Reported:

Username: <?= $reported->username ?>
Full Name: <?= $reported->renderFullname() ?>
Email : <?= $reported->email ?>

---
Reason : <?= $model->notes ?>

Message : <?= $message->message ?>

