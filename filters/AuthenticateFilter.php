<?php

namespace app\filters;

use \Yii;
use \yii\base\ActionFilter;
use \yii\helpers\Url;
use \app\models\User;

/**
 * Class AuthenticateFilter
 * @package app\modules\main\filters
 * @author Aleksandr Mokhonko
 * Date: 06.07.17
 */
class AuthenticateFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (null == Yii::$app->user->getId() || Yii::$app->user->identity->status !== User::STATUS_ACTIVE) {
            Yii::$app->getResponse()->redirect(Url::toRoute(['/site/login']));
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