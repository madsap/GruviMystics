<?php
use \yii\helpers\Url;
    
yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'expired_session_modal_alert_header'],
    'id' => 'expired_session_modal_alert',
    'size' => 'modal-lg',
    'closeButton' => false,
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
]);
?>

<div id='modalContent' style="color:red">
    Session has expired. Please <a href="<?= Url::to(['/site/login'], true);?>" style="font-weight: bold;">log in</a> again
</div>

<?php 
yii\bootstrap\Modal::end();
?>