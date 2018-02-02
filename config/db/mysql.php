<?php

return [
    'class'         => 'yii\db\Connection',
    'dsn'           => 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB,
    'username'      => MYSQL_USER,
    'password'      => MYSQL_PASSWORD,
    'charset'       => 'utf8mb4',
    'tablePrefix'   => 'md_',
];
