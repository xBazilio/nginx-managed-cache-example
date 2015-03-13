<?php

// change the following paths if necessary
require(__DIR__ . '/../vendor/autoload.php');
$config=dirname(__FILE__).'/../app/config/main.php';

// remove the following line when in production mode
// defined('YII_DEBUG') or define('YII_DEBUG',true);

Yii::createWebApplication($config)->run();
