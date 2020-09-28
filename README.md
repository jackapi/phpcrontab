# PHP 定时任务管理
- 支持Class执行和Command执行方式

#### 使用swoole4 PHP>=7.0

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
    'cache_data' => "./cache.data",
    'pid' => "./timer.pid",
    'swoole_tick' => [true, 1000],
    ['*/1 * * * *', 'test1', new \Jackapi\testCrontab(), 'whileTest'],
    ['*/2 * * * *', 'test2', new \Jackapi\testCrontab(), 'getDate'],
    ['*/3 * * * *', 'test3', 'date', ''],

];
$crontab = new Jackapi\phpcrontab($crontabData);
$crontab->run();

```

- debug 开启显示日志信息
- log 设置日志路径文件
- cache_data 设置开启swoole定时器使用，用来存储执行的定时器任务
- pid 存储定时器ID用于终止
- swoole_tick 开启定时器以及时间周期（毫秒）

- 新增
```php
新增swoole定时器
终止swoole定时器
```