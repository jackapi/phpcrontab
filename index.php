<?php


require_once 'vendor/autoload.php';

//crontab表达式 名称 类 执行方法
$crontabData = [
    'debug' => true,
    'log' => "./1.log",
    ['*/1 * * * *', 'test1', new \Jackapi\testCrontab(), 'whileTest'],
    ['*/2 * * * *', 'test2', new \Jackapi\testCrontab(), 'getDate'],
    ['*/3 * * * *', 'test3', 'date', ''],

];
$crontab = new Jackapi\phpcrontab($crontabData);
$crontab->run();
