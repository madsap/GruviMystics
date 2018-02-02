<?php

/**
 * @var \yii\web\View $this
 */

use app\components\widgets\ReadersTeaser;

$this->title = 'Main';
?>
<div class="jumbotron">
    <div class="container text-center">
        <h1 class="text-bold">Get answers right now!</h1>
        <div class="bottom text-pink h4">
            Get <span class="text-blue">$10</span> in <span class="text-orange text-bold">GruviBucks</span> for signing up today
        </div>
    </div>
</div>

<div id="readers_teaser_cnt"><?= ReadersTeaser::widget(); ?></div>

<div class="home_bottom text-default">
    <h3 class="text-bold">Why Gruvi Mystics?</h3>
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            Why pick Gruvi Mystics? <br/>
            We screen all of our readers and psychics through actual sessions and our bar is high.
            Bottomline, we only work with the best readers, psychics and mediums.
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 text-center">
                    <img src="../images/foto.jpg" class=" img-thumbnail"/>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12 text-center">
                    <img src="../images/foto.jpg" class=" img-thumbnail"/>
                </div>
            </div>
        </div>
    </div>
</div>