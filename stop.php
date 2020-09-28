<?php


require_once 'vendor/autoload.php';

//crontab表达式 名称 类 执行方法
$crontabData = [
    'pid' => "./timer.pid",
];
$crontab = new Jackapi\phpcrontab($crontabData);
$crontab->stop();
