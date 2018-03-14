<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\models\User;
use \yii\helpers\Url;


$this->title = 'About';
?>
<div class="page-wrapper tag-views_user_reader col-xs-12">
    <?php if (Yii::$app->session->hasFlash('success')) { ?>
        <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php } ?>
<?php 
$sessionUser = Yii::$app->user;
//var_dump($sessionUser->identity->role);
//die;
 ?>
    <div class="page-container panel panel-default">
        <div class="panel-body">

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'form-custom']]); ?>

            <div class="row">
                <div class="col-sm-6">
                    <div class="page-box-container panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <?php /*$form->field($model, 'role')->dropDownList([ 'super_admin' => 'Super admin', 'bar_admin' => 'Bar admin', 'user' => 'User', ], ['prompt' => ''])*/ ?>

                                <?php /*$form->field($model, 'registrationType')->dropDownList([ 'email' => 'Email', ], ['prompt' => ''])*/ ?>

                                <div class="col-md-6 col-sm-6">
                                    <?= $form->field($model, 'firstName')->textInput(['maxlength' => true]) ?>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <?= $form->field($model, 'lastName')->textInput(['maxlength' => true]) ?>
                                </div>
                            </div>
<?php if ( empty($is_action_add_reader) ) { ?>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <?= $form->field($model, 'displayname')->textInput(['maxlength' => true])->label('Display Name') ?>
                                    <div class="form-control-notes">up to 20 characters - this is the name that users will see on the listings page.</div>
                                </div>
                            </div>
<?php } else { ?>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <?= $form->field($model, 'username')->textInput(['maxlength' => true])->label('User Name') ?>
                                    <div class="form-control-notes">up to 20 characters - Please enter a unique username.</div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <?= $form->field($model, 'displayname')->textInput(['maxlength' => true])->label('Display Name') ?>
                                    <div class="form-control-notes">up to 20 characters - this is the name that users will see on the listings page.</div>
                                </div>
                            </div>
<?php } ?>
                            <?= $form->field($model, 'tagLine')->textInput(['maxlength' => true]) ?>
                            <div class="form-control-notes">up to 140 characters - this is the tagline that users will see on the listings page.</div>
                            <br/>
                            <?= $form->field($model, 'description')->textarea();?>

                            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <input type="password" style="display:none"><!-- disables autocomplete -->
                                    <?= $form->field($model, 'password')->passwordInput(['autocomplete' => "off"]) ?>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <?= $form->field($model, 'confirmPassword')->passwordInput(['autocomplete' => "off"]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-box-container panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <b>Specialties</b>
                                <input name="User[specialties]" id="specialties" value="<?= Html::encode($model->getSpecialties()); ?>" class="form-control" type="text"/>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-sm-6">
                    <div class="page-box-container panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <img src="<?= $model->getProfilePicUrl();?>" class="img-responsive"/>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <h2 class="text-pink text-center">Upload an image users will see</h2>
                                    <div class="small">
                                        Make sure image is PNG or JPEG.<br/>
                                        A JPEG will most likely look best.<br/>
                                        Size limit 2Mb.
                                    </div>
                                    <br/>
                                    <input type="file" name="<?= User::FILENAME ?>"  style="opacity: 0; cursor:pointer; height:34px;"/>
                                    <div class="btn btn_gruvi btn-block" style="margin-top:-36px;">Pick a photo</div>
                                    <?php if(!empty($fileErrors['photo'][0]))echo '<div style="font-weight:bold;color:red">'.$fileErrors['photo'][0].'</div>';?>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-pink">SET YOUR RATE</h2>
                    <div class="row">
                        <div class="col-sm-1 col-md-1 text-bold text-violet h4 text-right">$</div>
                        <div class="col-sm-6 col-md-6"> <?= $form->field($model, 'rate')->textInput(['maxlength' => true])->label(false) ?> </div>
                        <div class="col-sm-5 col-md-5 text-bold text-violet h4 text-left">/min</div>
                    </div>
                    <h2 class="text-pink">SET ROLE</h2>
                    <div class="tag-select_role" style="margin:30px 0px;">
                        <select name="User[role]" id="role" class="form-control">
                            <?php
                            $role = $model->role;
                            ?>
                            <option value="">Select Role</option>
                            <?php
                            if($role == User::ROLE_USER){
                            ?>
                            <option value="<?= User::ROLE_USER ?>" selected="selected"><?=User::ROLE_USER?></option>
                            <?php
                            }else{
                            ?>
                            <option value="<?= User::ROLE_USER ?>"><?=User::ROLE_USER?></option>
                            <?php
                            }
                            if($role == User::ROLE_READER){
                            ?>
                            <option value="<?= User::ROLE_READER ?>" selected="selected"><?=User::ROLE_READER?></option>
                            <?php
                            }else{
                            ?>
                            <option value="<?= User::ROLE_READER ?>"><?=User::ROLE_READER?></option>
                            <?php
                            }
                            /* %PSG: only admin user can upgrade users to admin */
                            if ( 'admin' == $sessionUser->identity->role ) {
                            if($role == User::ROLE_ADMIN){
                            ?>
                            <option value="<?= User::ROLE_ADMIN ?>" selected="selected"><?=User::ROLE_ADMIN?></option>
                            <?php
                            }else{
                            ?>
                            <option value="<?= User::ROLE_ADMIN ?>"><?=User::ROLE_ADMIN?></option>
                            <?php
                            }
                            } // admin == $sessionUser->role
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4 col-sm-10 col-sm-offset-2">
                            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn_gruvi btn-block']) ?>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>

    </div>
</div>

<?php ActiveForm::end(); ?>

<script src="<?= Url::to(['/jQuery-Tags-Input-master/dist/jquery.tagsinput.min.js'], true);?>"></script>
<link rel="stylesheet" type="text/css" href="<?= Url::to(['/jQuery-Tags-Input-master/dist/jquery.tagsinput.min.css'], true);?>" />

<script>
    $('#specialties').tagsInput();
</script>
