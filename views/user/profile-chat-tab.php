<?php 
    use \yii\helpers\Url;
?>

    <h3 class="text-default">
        CHAT 
        <a href="#" onclick='scrollToBottom("messages-list-cnt", 10);return false;'><img id='chat-new-messages-icon' src="<?= Url::to(['/images/np_chat_2.png'], true);?>" style="display:none;width:36px;"></a>
    </h3>
<!--    <div class="panel panel-default">
        <div class="bg-light-blue">-->
            <div class="panel-body" id="messages-list-cnt" style="max-height:650px;overflow:auto">
                <?php if(!empty($chat['html']))echo $chat['html']; ?>
            </div>
<!--        </div>
    </div>-->
    
    <?php if(!Yii::$app->user->isGuest){ ?>
    <div class="enter_message panel panel-info">
        <div class="panel-body">
            <form onsubmit="sendMessage(<?= $model->id; ?> , <?= Yii::$app->user->identity->id; ?>, $('#message-content').val());return false;">
                <input id="send-message-button" type="submit" value="ENTER" class="pull-right btn btn-green text-default">
                <textarea onkeyup="auto_grow(this)" required placeholder="ENTER MESSAGE HERE" id="message-content" style="resize: none;overflow: hidden;min-height: 50px;width:80%"></textarea>
            </form>
        </div>
    </div>
    <?php } ?>


<script>
    window.minMessageId = <?= !empty($chat['minMessageId'])?$chat['minMessageId']:0; ?>;
    //$("#messages-list-cnt").animate({ scrollTop: $('#bg-light-blue').prop("scrollHeight")}, 1000);
    $( document ).ready(function() {
        
        scrollToBottom("messages-list-cnt", 10);
        $('#message-content').keydown(function (e) {
            if (e.keyCode === 13 && e.ctrlKey) {
                //console.log("enterKeyDown+ctrl");
                $(this).val(function(i,val){
                    return val + "\n";
                });
            }
        }).keypress(function(e){
            if (e.keyCode === 13 && !e.ctrlKey) {
                $("#send-message-button").click();
                return false;  
            } 
        });
        
        $( "#messages-list-cnt" ).scroll(function() {
          if(isScrolledToBottom("messages-list-cnt"))$("#chat-new-messages-icon").hide();
        });
    });
    
</script>
