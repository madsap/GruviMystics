<?php

use \yii\helpers\Html;
use \yii\helpers\Url;
use \app\models\Call;

$this->title = 'Conversation';
?>

<div style="margin:0px 50px;">
     
    <div class="row">
        <div class="col-md-3 col-sm-3">
            <img src="<?= Url::to(['/images/np_microphone_2.png'], true);?>" alt="" class="img-responsive"/>
        </div>
        <div class="col-md-9 col-sm-9">
            <h3 class="text-pink">Outgoing call to:</h3>
            <h4 class="text-orange"><?= Html::encode($reader->firstName.' '.$reader->lastName); ?> </h4>
            <?php 
            if(empty($activeCall->status)){ ?>
                <div id="user-call-log"></div>
                <span id="start_call_cnt"> 
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <a href="#" onclick="customerStartCall('<?= Html::encode($reader->getNameForTwilio()); ?>', <?= $reader->rate; ?>); return false;" class="btn btn-green text-default text-uppercase btn-block">START CALL</a> 
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <a href="#" onclick="$('#call-details-modal').modal('hide');return false;" class="btn btn-default text-default text-uppercase btn-block">CANCEL</a>
                        </div>
                    </div>
                </span>
                <span id="end_call_cnt" style="display: none"> 
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3">
                            <a href="#" onclick="endCall();return false;" class="btn btn-green text-default text-uppercase btn-block">END CALL</a>
                        </div>
                    </div>
                </span>
                <input type="hidden" id="call-details-modal-no-auto-close">
            <?php 
                }else{ ?>
                <br>Status: <?= $activeCall->status; ?>
                <?php if($activeCall->status == Call::STATUS_CONVERSATION){ ?>
                    <h4 class="text-blue"><?= $activeCall->calculateDuration();?> </h4><br>
                    <?php if($currentCredit <= 2){ ?>
                        <div>
                            You are running out of credits. <a href="#" onclick='$("#add-gruvi-bucks-modal").modal("show");return false;'>Add more GruviBucks to your account</a>
                        </div>
                    <?php } ?>
                <?php } ?>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3">
                        <a href="#" onclick="endCall();" class="btn btn-green text-default text-uppercase btn-block">END CALL</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>