<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use \yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reader Activity';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-wrapper bg-default">
    <h3 class="text-pink" style="margin:20px 30px 0px 30px;">
        <?= Html::encode($this->title) ?>
        <a class="btn btn_gruvi pull-right" href="<?= Url::to(['/user/add-reader'], true);?>">ADD new Reader</a>
    </h3>
    <div class="page-container panel panel-default">

        <div class="user-index table table-striped" style="padding:0px 20px;">

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <?php
                if(!empty($readers)){
                    
                    foreach($readers as $reader){
                    ?>
                        <div>
                            <div class="pull-right">
                                <a href="<?= Url::to(['/user/reader/'.$reader->id], true);?>" class="text-pink">profile</a> / 
                                <a href="<?= Url::to(['/user/delete-reader?id='.$reader->id]);?>" class="text-danger">delete</a>
                            </div>
                            <div class="text-pink h4 text-bold"><?= Html::encode($reader->firstName.' '.$reader->lastName); ?></div>
                        </div>
                        <?php 
                            //echo $reader->id;exit;
                            $calls = $reader->getCallsReaders()->all();
//                            if(!empty($calls)){
                                //print_r($calls->callsReaders']);exit;
                            ?>
                                <table class="table table-striped h4 text-grey">
                                    
                            <?php
                                foreach ($calls as $call){
                                ?> 
                                    <tr>
                                        <td class="col-lg-2 col-md-2"><?= date("n/j/y", strtotime($call->createAt)); ?></td>
                                        <td class="col-lg-2 col-md-2"><?= round($call->duration/60, 1); ?> min</td>
                                        <td class="col-lg-4 col-md-2"><a href="<?= Url::to(['/user/profile/'.$call->customer->id], true);?>" class=" text-grey"><?= Html::encode($call->customer->firstName.' '.$call->customer->lastName); ?></a></td>
                                        <td class="col-lg-3 col-md-2">Call</td>
                                    </tr>
                    <?php       } 
                             ?>
                                </table>
                            <?php
//                            }


                    }
                }
            ?>
        </div>

    </div>
</div>