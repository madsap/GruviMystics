<?php
namespace app\components\widgets;
use app\models\User;

use yii\base\Widget;

class ReportUserAlert extends Widget
{
    public $params;

    public function init()
    {
        parent::init();
        //$this->params = [];
    }

    public function run()
    {
        
        $reportedUser = User::findIdentity($this->params['reported_id']);
        $html = $this->render('report_user_alert.php', ['reported_user'=>$reportedUser, 'params'=>$this->params]);
        return $html;
        
	}
}
