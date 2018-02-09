<?php
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'prod');
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();

/* Placeholder, removed by PSG for dev
<!Doctype html>
<html>
<head>
<link href='http://fonts.googleapis.com/css?family=Orbitron:'2000' rel='stylesheet' type='text/css'><style>display: block; margin:auto; width:231px;</style>
</head><body><p><center><img src="LogoFinal--600-Trans.png" width="231" height="37" alt=""/><font color="purple" size = large><p>GRUVI MYSTICS COMING SOON...<font></center></l></body>
</html>
*/
