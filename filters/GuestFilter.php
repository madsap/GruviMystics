<?php

namespace app\filters;

use \Yii;
use \yii\base\ActionFilter;
use \yii\helpers\Url;

/**
 * Class GuestFilter
 * @package app\filters
 * @author Aleksandr Mokhonko
 * Date: 07.07.17
 */
class GuestFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (false === Yii::$app->user->getIsGuest()) {
            Yii::$app->getResponse()->redirect(Url::toRoute(['/user/profile']));
            return false;
        } else {
            return parent::beforeAction($action);
        }
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}