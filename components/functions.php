<?php
// like dd in laravel
function hh($data)
{
    yii\helpers\VarDumper::dump($data, 10, true);
    //die;
    Yii::$app->end();
}
