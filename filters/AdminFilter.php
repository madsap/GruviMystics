<?php

namespace app\filters;

use \Yii;
use \yii\base\ActionFilter;
use \yii\web\ForbiddenHttpException;

/**
 * Class AdminFilter
 * @package app\modules\main\filters
 * @author Aleksandr Mokhonko
 * Date: 06.07.17
 */
class AdminFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if(false === Yii::$app->user->getIsGuest()) {
            return parent::beforeAction($action);
        } else {
            throw new ForbiddenHttpException('Forbidden.');
        }
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}