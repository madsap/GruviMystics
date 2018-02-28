<?php
use \yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
    

<div id='report_user_modal_cnt' class="crate-modal text-center">

    <h4>Report <span id="report_user_modal_name_placeholder"><?=$reported_user->renderFullname() ?></span>?</h4>
<!--
    <div>
        <textarea name="report_reason" value="" id="report_reason" placeholder="Enter Reason..."></textarea>
    </div>
    <div>
        <input type="button" value="REPORT" id="report_user_modal_submit_button" class="tag-clickme_to_report_user btn btn-red"> 
        <a href="#" class="tag-clickme_to_cancel text-muted">Cancel</a>
-->

    <div>
        <?= Html::beginForm(['user/report-ajax'], 'post', ['class'=>'form-report_user', 'enctype' => 'multipart/form-data']) ?>
        <?= Html::input('hidden', 'reporter_id', $params['reporter_id']) ?>
        <?= Html::input('hidden', 'reported_id', $params['reported_id']) ?>
        <?= Html::input('hidden', 'message_id', $params['message_id']) ?>
        <div>
        <?= Html::textarea('report_reason', null, ['rows'=>5, 'placeholder' => 'Enter reason...']) ?>
        </div>
        <div>
        <?= Html::submitButton('Submit', ['class' => 'tag-clickme_to_report_user submit btn btn-red']) ?>
        <?= Html::button('Cancel', ['class' => 'tag-clickme_to_cancel text-muted btn']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>


</div>





