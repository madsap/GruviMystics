<?php
use yii\helpers\Html;
use \app\models\User;
use \yii\helpers\Url;
?>
<div class="page-wrapper tag-views.user.profile">
    
    <?php if($model->getAttribute('role') == User::ROLE_READER){ ?>
    <div class="text-default bg-violet page-title-default">        
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <h3><?= Html::encode($model->firstName/*.' '.$model->lastName*/); ?></h3>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 text-center">
                <?php if($editable){ ?>
                <a href="<?= Url::to(['/user/reader/'.$model->id], true);?>" class="text-default"><h4>EDIT profile</h4></a>
                <?php } ?>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 text-right">
                <h2><?php /*if(!empty($model->username))echo '#'.Html::encode($model->username);*/ ?></h2>
            </div>
        </div>        
    </div>
    <div class="bg-grey clearfix">
    <div class="profile_container">
        <div class="profile_content">
            
            <div class="profile_top_container">
                <div class="reader_card clearfix" style="height:auto;">
                    <div class="photo" style="width:40%;">
                        <img src="<?= $model->getProfilePicUrl();?>" alt="" class="img-responsive">
                        <div class="photo_bottom text-default"><?= Html::encode($model->tagLine); ?></div>
                    </div>
                    <div class="content_reader_card">
                        <div class="text-orange">
                            text here
                        </div>
                        <?php 
                            if(!empty($specialties)){
                                echo '<div class="text-pink profile_container_tags text-uppercase">';
                                foreach($specialties as $tag){
                                    echo '<span class="profile_tags_cell">'.Html::encode($tag).'</span> '; 
                                }
                                echo '</div>';
                            }
                        ?>                
                        <div class="text-violet"><?= Html::encode($model->description); ?></div>
                    </div>
                </div>
                
<!--                    <div class="row">
                        <div class="col-md-3 col-sm-3">

                           <img src="<?= $model->getProfilePicUrl();?>" class="img-responsive" style="padding:15px 0px 0px 15px;">  style="width:192px;"
                        </div>
                        <div class="col-md-9 col-sm-9">
                            <div class="text-orange h4"><?= Html::encode($model->tagLine); ?></div>    
                            <?php 
                                if(!empty($specialties)){
                                    echo '<div class="text-pink profile_container_tags text-uppercase">';
                                    foreach($specialties as $tag){
                                        echo '<span class="profile_tags_cell">'.Html::encode($tag).'</span> '; 
                                    }
                                    echo '</div>';
                                }
                            ?>                
                            <div class="text-violet"><?= Html::encode($model->description); ?></div>
                        </div>
                    </div>-->
            </div>

            <div class="sidebar">
                <div class="profile_top_container_right ">
                    <div class="row">
                        <div class="col-md-5 text-pink">
                            rate: <span class="h3">$<?= $model->rate;?>/min</span>
                        </div>
                        <div class="col-md-7">
                            <?php if($editable){ ?>
                                <select onchange="changeReaderStatus(this);" class="select_state form-control">
                                    <option value="available" <?php if($model->activity != User::ACTIVITY_DISABLED)echo 'selected';?>>AVAILABLE</option>
                                    <option value="inactive" <?php if($model->activity == User::ACTIVITY_DISABLED)echo 'selected';?>>INACTIVE</option>
                                </select>
                                <?php if($model->isAvailableForCalls()){ ?>
                                    <div id="reader-log" class="text-center" >Connecting...</div>
                                <?php } ?>                    
                                <div class="text-grey h4">
                                    Calls this mo: <span class="pull-right"><?= (!empty($callsStatistic['calls_count'])?$callsStatistic['calls_count']:'-');?></span>
                                </div>
                                <div class="text-grey h4">
                                    Min. this mo: <span class="pull-right"><?= (!empty($callsStatistic['calls_duration'])?(round($callsStatistic['calls_duration']/60, 1)):'-');?></span>
                                </div>
                            <?php }else{ ?>
                                <div class="text-pink">status:  <span class="h3" id="reader-activity-cnt"><?= $model->activity; ?></span></div>
                                <br><br>
                                <div class="text-pink">
                                    credit: <span class="text-blue h3"><?= (!Yii::$app->user->isGuest && $model->rate > 0)?floor(Yii::$app->user->identity->getGruviBucksAmount()/$model->rate):"0"; ?>min</span>                        
                                </div>
                                <a href="<?= Url::to(['/gruvi-bucks/add'], true);?>" class="btn btn-block text-default text-bold btn_gruvi ">add more Gruvi Bucks</a>


                            <?php } ?>  
                        </div>
                    </div>
                </div>
            
                            <div class="profile_call_manage" >
              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h4 class="text-pink">VOICE CALL</h4>
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <?php if($editable){ ?>
                                <div class="title_row">
                                    <img src="<?= Url::to(['/images/np_microphone_1.png'], true);?>" alt="" class="icn"/> <span class="text_for_icn text-grey h4">Turn on Calls through website</span>
                                </div>
                                <div class="text-left" style="margin-left:20px;">
                                    <input id="voice" type="checkbox" onclick="setReaderOpt('voice', this.checked)" <?php if($model->opt_voice)echo 'checked'?> >
                                    <label class="text-violet h4" for="voice"> CALLS <?= ($model->opt_voice)?'on':'off'; ?></label>
                                </div>
                            <?php }else{ ?>
                                <div class="title_row">
                                    <img src="<?= Url::to(['/images/np_microphone_2.png'], true);?>" alt="" class="icn"/> 
                                </div>
                                <input id="reader-call-btn"  type="button" onclick="showCallDetails(<?= $model->id; ?>);" value="Call NOW" <?= (!$model->opt_voice)?'disabled':'';?> class="btn btn-green text-default" style="width:120px;">
                            <?php } ?> 
                        </div>
                    </div>
                </div>
<!--                <div class="col-md-4 col-sm-4 col-xs-12 text-center">
                    <h3 class="text-pink">CHAT</h3>
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <?php if($editable){ ?>
                                <div class="title_row">
                                    <img src="<?= Url::to(['/images/np_chat_1.png'], true);?>" alt="" class="icn"/><span class="text_for_icn text-grey h4">Turn on Chat through website</span>
                                </div>
                                <div class="text-left" style="margin-left:20px;">
                                    <input id="chat" type="checkbox" onclick="setReaderOpt('chat', this.checked)" <?php if($model->opt_chat)echo 'checked'?>>
                                    <label class="text-violet h4" for="chat"> CHAT <?= ($model->opt_chat)?'on':'off'; ?></label>
                                </div>
                            <?php }else{ ?>
                                <div class="title_row">
                                    <img src="<?= Url::to(['/images/np_chat_2.png'], true);?>" alt="" class="icn"/> 
                                </div>
                                <input id="reader-chat-btn" onclick="location.replace('<?= Url::to(['/chat?with='.$model->id], true);?>');" type="button" value="Chat NOW" <?= (!$model->opt_chat)?'disabled':'';?> class="text-default btn btn-green" style="width:120px;">
                            <?php } ?>  
                        </div>
                    </div>
                </div>-->
<!--                <div class="col-md-4 col-sm-4 col-xs-12 text-center">
                    <h3 class="text-pink">REQUEST a CALL</h3>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?php if($editable){ ?>
                                <div class="title_row">
                                    <img src="<?= Url::to(['/images/np_incoming-call.png'], true);?>" alt="" class="icn"/> <span class="text_for_icn text-grey h4">Turn on Request a Call</span>
                                </div>
                                <div class="text-left" style="margin-left:20px;">
                                    <input id="request" type="checkbox" onclick="setReaderOpt('request', this.checked)" <?php if($model->opt_request)echo 'checked'?> disabled="disabled">
                                    <label class="text-violet h4" for="request"> request <?= ($model->opt_request)?'on':'off'; ?></label>
                                </div>
                            <?php }else{ ?>
                                <div class="title_row">
                                    <span class="">enter your number to have<br/> Claire call you back</span><br/>
                                    <input id="request_a_call_input" type="text" placeholder="(   )    -" <?= (!$model->opt_request)?'disabled':'';?>>
                                </div>  
                              <input onclick="requestACall($('#request_a_call_input').val(), <?= $model->id;?>);" type="button" value="Chat NOW" <?= (!$model->opt_request)?'disabled':'';?> class="text-default btn btn-green" style="width:240px;">
                            <?php } ?>  

                        </div>
                    </div>
                </div>-->
              </div>
            </div>
            
            </div>



            
        </div>
        <div class="profile_chat_container">            
            <?= $this->render('profile-chat-tab', array('model'=>$model, 'editable' => $editable, 'chat' => $chat)); ?>
        </div>
    </div>

    


    <?php }elseif($model->getAttribute('role') == User::ROLE_USER){ ?>
        <h1 style="background-color: white"><?= Html::encode($model->firstName.' '.$model->lastName); ?></h1>
        <?php if($editable){ ?>
            <a href="<?= Url::to(['/user/update/'], true);?>">EDIT profile</a>
        <?php } ?>
        <div style="margin-top:20px; border: 1px solid gray;background-color:white;padding:10px;">GruviBucks: $<?= $model->getGruviBucksAmount();?></div>
    <?php } ?>
        
    </div>
</div>



<!-- <?//= $this->render('profile-chat-tab', array('model'=>$model, 'editable' => $editable, 'chat' => $chat)); ?> -->

