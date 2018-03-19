<?php

/**
 * @var \yii\web\View $this
 */

use \yii\helpers\Html;

$this->title = 'Notice: WebRTC';
?>
<div class="col-xs-12 non-rtc">
    <div class="row gPanel-header">
        <div class="col-xs-12 text-center">
            ATTENTION: You are using a browser that is not currently supported.
        </div>
        <div class="col-xs-12 text-center">
            Please use one of the following browsers or Download the app!
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-1 col-md-3 col-xs-offset-3 col-xs-5 gPanel">
            <div>Browsers that support WebRTC:</div>
            <ul>
                <li><em>Mac:</em>
                    <ul>
                        <li>FireFox</li>
                        <li>Chrome</li>
                    </ul>
                </li>
            </ul>
            <ul>
                <li><em>PC:</em>
                    <ul>
                        <li>Chrome</li>
                        <li>Explorer</li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="col-md-offset-1 col-md-2 col-xs-offset-3 col-xs-5 gPanel text-center">
            <div><em>iOS:</em></div>
            <div>App Store</div>
            <a href="#" class="logo"><img src="/images/apple.png" alt=""></a>
        </div>
        <div class="col-md-offset-1 col-md-2 col-xs-offset-3 col-xs-5 gPanel text-center">
            <div><em>Android:</em></div>
            <div>Play Store</div>
            <a href="https://play.google.com/store/apps/details?id=com.gruvimystics" class="logo"><img src="/images/play.png" alt=""></a>
        </div>
    </div>
</div>

