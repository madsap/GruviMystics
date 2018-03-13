<?php

use yii\helpers\Html;
use yii\helpers\Url;

/*
if (!empty($nextPageId)) {
    ?>
    <div style="text-align:center;">
        <input id="load-more-btn-<?= $nextPageId ?>" value="Load more"
               onclick="getMessages(<?= $readerId ?>, <?= $nextPageId ?>);" class="btn btn-green text-default"
               type="button">
        <hr>
    </div>
    <?php
}
*/
if (!empty($messages)) {
    $current_date_line = "";
    foreach ($messages as $message) {
        $message_timestamp = strtotime($message->createAt);
        $string_date = date("Y-m-d", $message_timestamp);
        $date_div_id = $string_date;

        if ($string_date == date("Y-m-d")) {
            $new_date_line = "Today";
            $date_div_id = "Today";
        } elseif ($string_date == date("Y-m-d", time() - 24 * 3600)) {
            $new_date_line = "Yesterday";
        }  elseif ($message_timestamp > time() - (7 * 24 * 3600)) {
            $new_date_line = strtoupper(date("l", $message_timestamp));
        } else {
            $new_date_line = strtoupper(date("M d, Y", $message_timestamp));
        }

        if ($new_date_line != $current_date_line) {
            $current_date_line = $new_date_line;
            echo '<div class="col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4 chat-date" id="chat-message-row-header-' . $date_div_id . '"><span class="text-grey">' . $current_date_line . '</span></div>';
        }

        ?>

        <div class="card row" id="chat-message-row-<?= $message->id; ?>">
            <div class="col-sm-2">
                <?php if ($message->readerId == $message->customerId) { ?>
                    <?php if( !$message->amISender() ) { ?>
                        <div class="text-grey chat-time text-right">
                            <a onclick="confirmBlockUser(<?= $message->customerId.', '.$message->id; ?>);return false;" id="chat_profile_user_lnk_<?= $message->id; ?>">
                                <?= Html::encode($message->customer->firstName); ?></a>
                            <span>
                                    <a class="text-orange tag-clickme_to_show_report_modal"
                                       data-route="<?= Url::toRoute(['/user-relation/partial','partial'=>'report_user']) ?>"
                                       data-reporter_id="<?= $message->readerId ?>"
                                       data-reported_id="<?= $message->customerId ?>"
                                       data-message_id="<?= $message->id ?>"
                                    >...</a>
                                </span>
                            </a>
                        </div>
                        <div class="text-grey chat-time text-right"><?= date("g:i a", $message_timestamp); ?></div>
                    <?php }else{ ?>
                            <div class="text-grey chat-time text-right"><?= date("g:i a", $message_timestamp); ?></div>
                        <?php } ?>
                <?php } ?>
            </div>
            <div class="col-md-8 col-sm-8 chat-message">
                <div class="card-body text-purple">
                    <?php if ($message->editable($myId)) { ?>
                        <a href="#"
                           onclick="deleteMessage(<?= $message->id ?>, <?= ($myId == $message->readerId) ? "true" : "false" ?>); return false;"
                           class="delete_message"><span class="glyphicon glyphicon-remove-circle text-grey"
                                                        aria-hidden="true"></span></a>
                    <?php } ?>
                    <?php if ($message->readerId == $message->customerId) { ?>
                        <div class="reader">
                    <?php } else { ?>
                        <div class="client">
                    <?php } ?>
                            <?= nl2br(Html::encode($message->message)); ?>
                        </div>
                </div>
            </div>
            <div class="col-sm-2">
                <?php if ($message->readerId != $message->customerId) { ?>
                    <div class="chat-time">
                        <?php if( !$message->amISender() ) { ?>
                            <div class="text-grey text-bold"><a onclick="confirmBlockUser(<?= $message->customerId.', '.$message->id; ?>);return false;" id="chat_profile_user_lnk_<?= $message->id; ?>">
                                    <?= Html::encode($message->customer->firstName); ?></a>
                                <span>
                                    <a class="text-orange tag-clickme_to_show_report_modal"
                                       data-route="<?= Url::toRoute(['/user-relation/partial','partial'=>'report_user']) ?>"
                                       data-reporter_id="<?= $message->readerId ?>"
                                       data-reported_id="<?= $message->customerId ?>"
                                       data-message_id="<?= $message->id ?>"
                                    >...</a>
                                </span>
                            </div>
                        <?php }else{ ?>
                            <div class="text-grey text-bold" id="chat_profile_user_lnk_<?= $message->id; ?>"><?= Html::encode($message->customer->firstName); ?></div>
                        <?php } ?>
                        <div class="text-grey"><?= date("g:i a", $message_timestamp); ?></div>
                    </div>
                <?php } ?>
            </div>

        </div>
        <?php
    }
}

?>
