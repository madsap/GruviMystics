<?php

require_once __DIR__ . '/_localConfig.php';
$params = require(__DIR__ . '/params.php');
$mysql  = require(__DIR__ . '/db/mysql.php');

$config = [
    'id'         => 'basic',
    'name'       => 'Gruvi Mystics',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'components' => [
        'request'      => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'qsHgBU-DKxfFJtqDEzbOQ-8yeKHVPnZz',
        ],
        'cache'        => [
            'class' => '\yii\caching\FileCache',
        ],
        'session'      => [
            'class'        => '\yii\web\DbSession',
            'db'           => 'db',
            'sessionTable' => '{{%user_session}}',
            'cookieParams' => [
                'httpOnly' => true,
            ],
        ],
        'user'         => [
            'identityClass'   => '\app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'imageResize' => [
			'class' => '\app\components\ImageResizeHelper',
		],
        'Yii2Twilio' => [
            'class' => 'filipajdacic\yiitwilio\YiiTwilio',
            'account_sid' => TWILIO_ACCOUNT_SID,
            'auth_key' => TWILIO_AUTH_TOKEN,
        ],
        'mailer'       => [
            'class' => 'yii\swiftmailer\Mailer',
            //'useFileTransport' => false,
            'useFileTransport' => YII_ENV_DEV ? true : false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => SMTP_HOST,
                'username' => SMTP_USERNAME,
                'password' => SMTP_PASSWORD,
                'port' => SMTP_PORT,
                'encryption' => 'tls',
            ],
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'   => '\yii\log\FileTarget',
                    'levels'  => ['error'],
                    'logVars' => ['error'],
                    'logFile' => '@runtime/logs/error.log'
                ],
                [
                    'class'   => '\yii\log\FileTarget',
                    'levels'  => ['warning'],
                    'logVars' => ['warning'],
                    'logFile' => '@runtime/logs/warning.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'categories' => ['twilioVoice'],
                    'logFile'    => '@runtime/logs/twilio_voice.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'categories' => ['twilioFallback'],
                    'logFile'    => '@runtime/logs/twilio_fallback.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'categories' => ['twilioStatus'],
                    'logFile'    => '@runtime/logs/twilio_status.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'categories' => ['paypalReturn'],
                    'logFile'    => '@runtime/logs/paypal_return.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'categories' => ['paypalCancel'],
                    'logFile'    => '@runtime/logs/paypal_cancel.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'categories' => ['paypalNotify'],
                    'logFile'    => '@runtime/logs/paypal_notify.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'logVars'    => ['show'],
                    'categories' => ['show'],
                    'logFile'    => '@runtime/logs/show.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'logVars'    => ['facebookError'],
                    'categories' => ['facebookError'],
                    'logFile'    => '@runtime/logs/facebookError.log'
                ],
                [
                    'class'      => '\yii\log\FileTarget',
                    'levels'     => ['info'],
                    'logVars'    => ['linkedinError'],
                    'categories' => ['linkedinError'],
                    'logFile'    => '@runtime/logs/linkedinError.log'
                ],
                [
                    'class'      => '\yii\log\DbTarget',
                    'levels'     => ['info'],
                    'categories' => ['db_log'],
                ],
            ],
        ],
        'db'           => $mysql,
        'urlManager'   => [
            'class'           => '\yii\web\UrlManager',
            // Disable index.php
            'showScriptName'  => false,
            // Disable r= routes
            'enablePrettyUrl' => true,
            'rules'           => [
                'admin/users/blocked/<id:\d+>'                               => 'admin/user/show-blocked',
                'admin/users/blocked'                                        => 'admin/user/index-blocked',

				'message/<id:\d+>'                                           => 'message/view',
				'message/view/<id:\d+>'                                      => 'message/view',
				'message/update/<id:\d+>'                                    => 'message/update',
				'message/delete/<id:\d+>'                                    => 'message/delete',
                'user/profile/<id:\d+>'                                      => 'user/profile',
                'user/reader/<id:\d+>'                                       => 'user/reader',
                'api/<_version:\d+>/<controller:(\w|\-)+>'                   => 'apiVersion<_version>/<controller>',
                'api/<_version:\d+>/<controller:(\w|\-)+>/<action:(\w|\-)+>' => 'apiVersion<_version>/<controller>/<action>',
                'file/<action>/<id:\w+\.?\w*>'                               => 'file/<action>',

                // %PSG : Site Controller actions
                //'/<action:auth>'                                             => 'site/<action>', // for facebook login/registration
                '/<action:terms-and-service>'                                => 'site/<action>', 
                '/<action:privacy-policy>'                                   => 'site/<action>',
                '/<action:login>'                                            => 'site/<action>',
                '/<action:about>'                                            => 'site/<action>',
                '/<action:request-password-reset>'                           => 'site/<action>',
                '/<action:reset-password>'                                   => 'site/<action>',

                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\w+>'        => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>'                 => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<id:\d+>'                     => '<module>/<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\w+>'                     => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>'                              => '<controller>/<action>',
                '<controller:\w+>/<id:\d+>'                                  => '<controller>/view',


                //'<action:\w+>'                                  => 'site/<action>', // %PSG
                //'<alias:[\w-]+>' => 'site/<alias>', // %PSG
            ],
        ],
        'authClientCollection' => [
          'class' => 'yii\authclient\Collection',
          'clients' => [
            'facebook' => [
              'class' => 'yii\authclient\clients\Facebook',
              'authUrl' => 'https://www.facebook.com/dialog/oauth?display=popup',
              'clientId' => '1485801698169263', //'275969346233372' - test, 1485801698169263 - live
              'clientSecret' => 'e587052fc67bd062ec829ffda9655060', //'1db81223fbbc362826a30c9ff4ef2703' - test, e587052fc67bd062ec829ffda9655060 - live
              'attributeNames' => ['name', 'email', 'first_name', 'last_name', 'birthday'],
              'scope' => ['user_birthday', 'email'],
            ],
          ],
        ],
    ],
    'params'     => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]      = 'debug';
    $config['modules']['debug'] = [
        'class' => '\yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.33.1'],
    ];

    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class'      => '\yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.33.1'],
    ];
}

return $config;
