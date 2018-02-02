<?php
use \yii\helpers\Url;
    
yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'block_user_alert_header'],
    'id' => 'block_user_alert_modal',
    'size' => 'modal-sm',
    'closeButton' => false
]);
?>

<div id='block_user_modal_cnt' class="text-center">

    <h4>Block <span id="block_user_modal_name_placeholder">-</span>?</h4>
    <div>
        <input type="button" value="BLOCK" id="block_user_modal_submit_button" class="btn btn-red"> 
        <a href="#" onclick="$('#block_user_alert_modal').modal('hide');return false;" class="text-muted">Cancel</a>
    </div>
</div>

<?php 
yii\bootstrap\Modal::end();
?>