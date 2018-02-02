<?php

namespace app\components\widgets;

use yii\base\Widget;

class ExpiredSessionAlert extends Widget
{
    /**
     * @return string
     */
    public function run()
    {
        
        $html = $this->render('expired_session_alert.php', []);
        return $html;
        
	}
}