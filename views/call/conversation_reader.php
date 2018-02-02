<?php
use \yii\helpers\Html;
use \app\models\Call;
use \yii\helpers\Url;

$this->title = 'Conversation';
?>

<div style="margin:0px 50px;">
    
    <!-- Status: <?= $activeCall->status; ?> -->
    <div class="row">
        <div class="col-md-3 col-sm-3">
            <img src="<?= Url::to(['/images/np_microphone_2.png'], true);?>" alt=""/>
        </div>
        <div class="col-md-9 col-sm-9">
            <h3 class="text-pink">Incoming call from :</h3>
            <h4 class="text-orange"><?php echo Html::encode($customer->firstName.' '.$customer->lastName); ?></h4>
            <div>Current credit: <?= $currentCredit; ?> min</div>
            <div id="reader-call-log"></div>
            <div class="row">
                <?php 
                if($activeCall->status == Call::STATUS_CONNECTION){  ?>
                    <div class="col-md-6 col-sm-6">
                        <a href="#" onclick="answerReaderCall();" class="btn btn-green text-default text-uppercase btn-block " id="reader_call_answer_lnk">ANSWER</a> 
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <a href="#" onclick="rejectReaderCall();" class="btn btn-default text-default text-uppercase btn-block">REJECT CALL</a>
                    </div>
                <?php }elseif($activeCall->status == Call::STATUS_CONVERSATION){ ?>
                    <h4 class="text-blue">Connected... <?= $activeCall->calculateDuration();?> </h4><br>
                    
                    <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3"><a href="#" onclick="endCall();" class="btn btn-green text-default text-uppercase btn-block">END CALL</a></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


