<?php

namespace app\components\widgets;

use app\models\search\User as UserSearch;
use yii\base\Widget;

class CallDetails extends Widget
{
    /**
     * @return string
     */
    public function run()
    {   
        return $this->render('call_details.php', []);
        
	}
}