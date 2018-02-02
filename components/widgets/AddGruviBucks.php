<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;

class AddGruviBucks extends Widget
{
    /**
     * @return string
     */
    public function run()
    {  
        if(Yii::$app->user->isGuest)return "";
        
        $creditCard = Yii::$app->user->identity->getDefaultCreditCard();
        return $this->render('add_gruvi_bucks.php', ['creditCard' => $creditCard]);
        
	}
}