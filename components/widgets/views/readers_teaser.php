<?php 
use yii\helpers\Html;
use yii\widgets\LinkPager;
use \app\models\User;
use \yii\helpers\Url;
?>

<div class="main_container">
    <div class="main_container_header">
        <div class="pull-right num_readers text-yellow h4">
            <input type="text" onchange="$('#reader_keyword_input').prop('disabled', true);refreshReadersTeaser('<?= $filter['activity']; ?>', 0);" placeholder="Search.." style="color:black" id="reader_keyword_input" value="<?= Html::encode($filter['keyword']); ?>">
            <a href="#" onclick="refreshReadersTeaser('', 0);return false;" class="text-yellow <?php if(empty($filter['activity']) || $filter['activity'] != User::ACTIVITY_ONLINE)echo 'h3 text-bold'; ?>"><u>All</u></a> (<?= $readers['total_count']; ?>)&nbsp; 
            <a href="#" onclick="refreshReadersTeaser('<?= User::ACTIVITY_ONLINE ?>', 0);return false;" class="text-yellow <?php if($filter['activity'] == User::ACTIVITY_ONLINE)echo 'h3 text-bold'; ?>">Available Now (<?= $readers['available']; ?>)</a>
        </div>
        <div class="pull-left h3 text-default text-bold">Meet our Readers</div>
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
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <a href="<?= Url::to(['/user/profile/'.$item->id], true);?>">
                        <div class="reader_card">
                            <div class="photo">
                                <img src="<?= $item->getProfilePicUrl();?>" alt=""/>
                                <div class="photo_bottom text-default"><?= Html::encode($item->firstName); ?></div>
                            </div>
                            <div class="content_reader_card">
                                <div class="text-pink">
                                    <span class="text-violet">$</span>
                                    <span class="h4 text-bold"><?= $item->rate; ?></span>
                                    <span>/min</span>
                                </div>
                                <div class="text-orange h4">
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
                    </a>
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
