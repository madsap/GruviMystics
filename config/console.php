<?php

require_once __DIR__ . '/_localConfig.php';
$params = require(__DIR__ . '/params.php');
$mysql  = require(__DIR__ . '/db/mysql.php');

$config = [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'components'          => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log'   => [
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
        'db'    => $mysql,
    ],
    'params'              => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
