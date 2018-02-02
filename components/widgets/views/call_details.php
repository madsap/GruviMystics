<?php 
use \app\models\Call;
use \yii\helpers\Url;

yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'call-details-modal-header'],
    'id' => 'call-details-modal',
    'size' => 'modal-lg',
    'closeButton' => false,
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
]);
?>

<div id="call-details-modal-placeholder" >
    <h1>Loading...</h1>
</div>

<?php 
yii\bootstrap\Modal::end();
?>

<?php /*if(!Yii::$app->user->isGuest && !empty(Call::getActiveCall(Yii::$app->user->identity))){ ?>
        <script type="text/javascript">
            //call in progress
            jQuery(document).ready(function () {
                showCallDetails(0);
            });
        </script>
<?php }*/ ?>