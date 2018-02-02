<?php
use yii\helpers\Html;


if(!empty($nextPageId)){
?>
    <div style="text-align:center;">
        <input id="load-more-btn-<?= $nextPageId ?>" value="Load more" onclick="getMessages(<?= $readerId ?>, <?= $nextPageId ?>);" class="btn btn-green text-default" type="button">
        <hr>
    </div>
<?php
}

if(!empty($messages)){
    $current_date_line = "";
    foreach($messages as $message){
        $message_timestamp = strtotime($message->createAt);
        $string_date = date("Y-m-d", $message_timestamp);
        $date_div_id = $string_date;
        
        if($string_date == date("Y-m-d")){
            $new_date_line = "TODAY";
            $date_div_id = "today";
        }elseif($string_date == date("Y-m-d", time()-24*3600)){
            $new_date_line = "YESTERDAY";
        }elseif($message_timestamp > time()-(7*24*3600)){
            $new_date_line = strtoupper(date("l", $message_timestamp));
        }else{
            $new_date_line = strtoupper(date("M d, Y", $message_timestamp));
        }
        
        if($new_date_line != $current_date_line){
            $current_date_line = $new_date_line;
            echo '<div class="col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4" id="chat-message-row-header-'.$date_div_id.'"><h4>'.$current_date_line.'</h4></div>';
        }
        
    ?>

        <div class="chat_message_row row" id="chat-message-row-<?= $message->id; ?>">
            <div class="col-md-4 col-sm-4">
                <?php if($myId == $message->readerId && $myId != $message->customerId){ ?>
                        <a href="#" onclick="confirmBlockUser(<?= $message->customerId.', '.$message->id; ?>);return false;" id="chat_profile_user_lnk_<?= $message->id; ?>" class="text-<?= ($message->customerId == $message->readerId)?"pink":"blue" ?>">
                            <?= Html::encode($message->customer->firstName.' '.$message->customer->lastName);?>
                        </a>
                <?php }else{ ?>
                        <span class="text-<?= ($message->customerId == $message->readerId)?"pink":"blue" ?>">
                            <?= Html::encode($message->customer->firstName.' '.$message->customer->lastName);?>
                        </span>
                <?php } ?>
                <div><?= date("H:i", $message_timestamp);?></div>
            </div>
            <div class="field_message col-md-8 col-sm-8">
                <?php if($message->editable($myId)){?>
                        <a href="#" onclick="deleteMessage(<?= $message->id ?>, <?= ($myId == $message->readerId)?"true":"false" ?>); return false;" class="delete_message"><span class="glyphicon glyphicon-remove-circle text-grey" aria-hidden="true"></span></a>
                <?php } ?>
                <?= nl2br(Html::encode($message->message)); ?>
            </div>

        </div>
    <?php
    }
}

?>