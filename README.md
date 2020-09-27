# PHP 定时任务管理
- 支持Class执行和Command执行方式

#### 使用swoole PHP>=7.0

- 安装方式
```php
composer require jackapi/phpcrontab dev-master
```

- 使用第三方
```php
cron解析 https://github.com/dragonmantank/cron-expression
```

- 使用方法
```php
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

```