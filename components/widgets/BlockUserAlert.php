<?php

namespace app\components\widgets;

use yii\base\Widget;

class BlockUserAlert extends Widget
{
    /**
     * @return string
     */
    public function run()
    {
        
        $html = $this->render('block_user_alert.php', []);
        return $html;
        
	}
}