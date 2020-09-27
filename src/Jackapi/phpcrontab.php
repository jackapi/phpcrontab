<?php

namespace Jackapi;


use Cron\CronExpression;

class phpcrontab
{
    public $crontab = [];

    public $log = '';
    public $debug = false;

    /**
     * crontab
     *
     * phpcrontab constructor.
     * @param array $cronrab
     */
    public function __construct($crontab = [])
    {
        if (!empty($crontab['debug'])) {
            $this->debug = $crontab['debug'];
            unset($crontab['debug']);
        }
        if (!empty($crontab['log'])) {
            $this->log = $crontab['log'];
            unset($crontab['log']);
        }
        $this->crontab = $crontab;
    }

    public function run()
    {
        \Co\run(function () {
            for ($i = 0; $i < count($this->crontab); $i++) {
                $this->one($this->crontab[$i]);
            }
        });
    }

    public function getDate()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 执行每一个
     * @param $data
     */
    private function one($data)
    {
        go(function () use ($data) {
            $log = "";
            $type = 'class';
            $date = $this->getDate();
            list($crontab, $name, $class, $fun) = $data;
            //判断是否是传入方法
            if (method_exists($class, $fun)) {
                $type = "class";
            } else {
                $type = "command";
            }
            //解析crontab 时间
            $cron = \Cron\CronExpression::factory($crontab);
            $next = $cron->getNextRunDate(date('Y-m-d H:i:s'), 0)
                ->format('Y-m-d H:i:s');
            //阻塞并等待下一步执行
            while (true) {
                \Swoole\Coroutine\System::sleep(0.5);
                if ($this->debug) {
                    $nowDate = date('Y-m-d H:i:s');
                    echo "{$name} while {$nowDate} {$next}..." . PHP_EOL;
                }
                if (date('Y-m-d H:i:s') == $next) {
                    break;
                }
            }
            //执行我们的代码
            $log .= "{$date}|{$name}[{$crontab}]: ";
            $result = "";
            //执行类
            if ($type == 'class') {
                $result = $class::$fun();
            }
            //执行命令行
            if ($type == 'command') {
                $result = exec($class . $fun);
            }
            $log .= "{$result}" . PHP_EOL;
            //判断日志路径
            if (!empty($this->log)) {
                $logs = new log($this->log);
                $logs->write($log);
            }
            if ($this->debug) {
                echo $log . PHP_EOL;
            }
        });
    }
}