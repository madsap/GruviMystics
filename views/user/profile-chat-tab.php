<div class="panel-body chat-panel" id="messages-list-cnt">
    <?php if (!empty($chat['html'])) echo $chat['html']; ?>
</div>

<?php if (!Yii::$app->user->isGuest) { ?>
    <div class="enter_message panel panel-info">
        <div class="panel-body col-xs-12">
            <div class="row no-gutter">
                <form onsubmit="sendMessage(<?= $model->id; ?> , <?= Yii::$app->user->identity->id; ?>, $('#message-content').val());return false;">
                    <div class="col-xs-10">
                    <textarea onkeyup="auto_grow(this)" required placeholder="Enter message here"
                              id="message-content"></textarea>
                    </div>
                    <div class="col-xs-2">
                        <input id="send-message-button" type="submit" value="enter" class="">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    window.minMessageId = <?= !empty($chat['minMessageId']) ? $chat['minMessageId'] : 0; ?>;
    $(document).ready(function () {

        scrollToBottom("messages-list-cnt", 10);
        $('#message-content').keydown(function (e) {
            if (e.keyCode === 13 && e.ctrlKey) {
                $(this).val(function (i, val) {
                    return val + "\n";
                });
            }
        }).keypress(function (e) {
            if (e.keyCode === 13 && !e.ctrlKey) {
                $("#send-message-button").click();
                return false;
            }
        });

        $("#messages-list-cnt").scroll(function () {
            if (isScrolledToBottom("messages-list-cnt")) $("#chat-new-messages-icon").hide();
        });
    });

</script>
