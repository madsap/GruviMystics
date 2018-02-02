<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use \app\models\User;

class Twilio extends Widget
{
    public $user;
    /**
     * @return string
     */
    public function run()
    {  
        $clientName = $this->user->getNameForTwilio();

        $capability = new ClientToken(Yii::$app->params['twilio']['accountSid'], Yii::$app->params['twilio']['authToken']);
        $capability->allowClientOutgoing(Yii::$app->params['twilio']['appSid']);
        $capability->allowClientIncoming($clientName);
        $token = $capability->generateToken();
        
        return $this->render('twilio_'.$this->user->getAttribute('role').'.php', ['token' => $token, 'clientName' => $clientName]);
        
	}
}