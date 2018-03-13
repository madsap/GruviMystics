<?php

use yii\helpers\Html;
use \app\models\User;

?>

<div class="profile col-xs-12">
    <div class="row profile-header">
        <div class="card col-xs-5 col-xs-offset-1">
            <div class="card-body">
                <div class="reader-card">
                    <div class="col-xs-4 left-col">
                        <div class="photo">
                            <!--                            <img class="ratio img-responsive img-circle" src="https://placeimg.com/100/100/any" alt="">-->
                            <img class="ratio img-responsive img-circle" src="<?= $model->getProfilePicUrl(); ?>"
                                 alt=""/>
                        </div>
                        <div class="price-text">
                            <span>$<?= $model->rate; ?></span><span class="suffix">/min</span>
                        </div>
                    </div>
                    <div class="col-xs-8 right-col">
                        <div class="header">
                            <?= Html::encode($model->firstName); ?>
                        </div>
                        <div class="content_reader_card">
                            <div class="tagline">
                                <?= Html::encode($model->tagLine); ?>
                            </div>
                        </div>
                        <div class="status-group col-xs-10 col-xs-offset-2 col-md-8 col-md-offset-4">
                            <?php if ($editable) { ?>
                                <div class="btn-group status-btns" role="group" aria-label="Select status">
                                    <button type="button" onclick="changeReaderStatus('active');" class="btn btn-sm <?php echo ($model->activity == User::ACTIVITY_DISABLED) ? 'btn-default' : 'btn-primary active';?>">Available</button>
                                    <button type="button" onclick="changeReaderStatus('inactive');" class="btn btn-sm <?php echo ($model->activity == User::ACTIVITY_DISABLED) ? 'btn-primary active' : 'btn-default';?>">Inactive</button>
                                </div>
                                <div class="metrics">
                                    Calls this mo: <span
                                            class="pull-right"><?= (!empty($callsStatistic['calls_count']) ? $callsStatistic['calls_count'] : '-'); ?></span>
                                </div>
                                <div class="metrics">
                                    Min. this mo: <span
                                            class="pull-right"><?= (!empty($callsStatistic['calls_duration']) ? (round($callsStatistic['calls_duration'] / 60, 1)) : '-'); ?></span>
                                </div>
                            <?php } else { ?>
                                <div>
                                    <?php if ($model->activity == User::ACTIVITY_ONLINE) { ?>
                                        <span class="available">Available</span>
                                    <?php } ?>
                                    <?php if ($model->activity == User::ACTIVITY_SESSION) { ?>
                                        <span class="">On Call</span>
                                    <?php } ?>
                                    <?php if ($model->activity == User::ACTIVITY_OFFLINE) { ?>
                                        <span class="">Offline</span>
                                    <?php } ?>
                                </div>
                                <div>
                                    <button class="btn btn-primary gruvi-btn" role="button">Talk</button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-5 col-xs-offset-1">
            <div class="title-container">
                <span class="title">Specialties</span>
            </div>
            <?php
            if (!empty($specialties)) {
                echo '<div class="specialty-group">';
                foreach ($specialties as $tag) {
                    echo '<div class="col-xs-6"><span class="profile_tags_cell">' . Html::encode($tag) . '</span></div>';
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="profile-chat col-xs-7">
            <div class="card profile-card chat">
                <div class="card-body">
                    <div class="title-container">
                        <span class="title">Chat</span>
                    </div>
                    <div>
                        <?= $this->render('profile-chat-tab', array('model' => $model, 'editable' => $editable, 'chat' => $chat)); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-chat col-xs-5">
            <div class="card profile-card profile">
                <div class="card-body">
                    <div class="title-container">
                        <span class="title">Profile</span>
                    </div>
                    <div class="profile-text">
                        <?= Html::encode($model->description); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="home_bottom col-xs-12"></div>
