<?php 
use yii\helpers\Html;
use yii\widgets\LinkPager;
use \app\models\User;
use \yii\helpers\Url;
?>

<div class="main_container">
    <div class="main_container_header">
        <div class="num_readers">
            <a href="#" onclick="refreshReadersTeaser('', 0);return false;" class="text-orange <?php if(empty($filter['activity']) || $filter['activity'] != User::ACTIVITY_ONLINE)echo 'text-bold active'; ?>">All (<?= $readers['total_count']; ?>)</a>
            <a href="#" onclick="refreshReadersTeaser('<?= User::ACTIVITY_ONLINE ?>', 0);return false;" class="text-orange <?php if($filter['activity'] == User::ACTIVITY_ONLINE)echo 'text-bold active'; ?>">Available Now (<?= $readers['available']; ?>)</a>
            <a href="#" class="text-orange">Recent (0)</a>
        </div>
    </div>
    <div class="main_container_pagination" style="text-align:right">
        <?= LinkPager::widget([
            'pagination' => $readers['pages'],
            'linkOptions' => ['class' => 'page_lnk']
        ]);
        ?>
    </div>
    <div class="main_container_content">
        <div class="row">
        <?php 
        if($readers['readers']){
            foreach($readers['readers'] as  $item){ ?>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <a href="<?= Url::to(['/user/profile/'.$item->id], true);?>">
                                <div class="reader-card-new">
                                    <div class="col-xs-6 left-col">
                                        <div class="photo">
                                            <img class="ratio img-responsive img-circle" src="https://placeimg.com/100/100/any" alt="">
                                            <!-- <img class="ratio img-responsive img-circle" src="<?= $item->getProfilePicUrl();?>" alt=""/> -->
                                        </div>
                                        <div class="price-text">
                                            <span>$<?= $item->rate; ?>/min</span>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 right-col">
                                        <div class="card-text">
                                            <?= Html::encode($item->firstName); ?>
                                        </div>
                                        <div class="content_reader_card">
                                            <div class="card-text">
                                                <?= Html::encode($item->tagLine); ?>
                                            </div>

                                            <?php if($item->activity == User::ACTIVITY_ONLINE){ ?>

                                                <?php if($item->opt_voice){ ?>
                                                    <!-- <a href="#" onclick="showCallDetails(<?= $item->id; ?>);return false;">Call NOW</a> -->
                                                    <div class="bottom bg-green">Call NOW</div>
                                                <?php }elseif($item->opt_chat){ ?>
                                                    <div class="bottom bg-green">Chat NOW</div>
                                                <?php }else{ ?>
                                                    <div class="bottom bg-grey">Offline</div>
                                                <?php } ?>

                                            <?php } ?>
                                            <?php if($item->activity == User::ACTIVITY_SESSION){ ?>
                                                <div class="bottom bg-orange">In Session</div>
                                                <div class="bottom bg-grey" style="display:none;">In Session</div>
                                            <?php } ?>
                                            <?php if($item->activity == User::ACTIVITY_OFFLINE){ ?>
                                                <div class="bottom bg-light-grey" style="display:none;">Offline</div>
                                                <div class="bottom bg-grey">Offline</div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>


        </div>
    </div>
</div> 

<script>
    if(typeof window.RTTimout === 'undefined'){
        //window.RTTimout = setTimeout("refreshReadersTeaser('<?= $filter['activity']; ?>', 0);", 4096); // %PSG: disable to fix pagination jump
        $('.page_lnk').click(function() {
            refreshReadersTeaser('<?= $filter['activity']; ?>', $(this).attr("data-page"));
            return false;
        })
    }
</script>
