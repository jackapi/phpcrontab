<?php

namespace Jackapi;


use Cron\CronExpression;

class phpcrontab
{
    public $crontab = [];

    public $log = '';
    public $debug = false;
    public $swooleTickMs = 0;
    public $swooleTick = false;
    public $cacheData = '';
    public $pid = 0;
    public $pidFile = '';
    /**
     * 正在执行的任务
     * @var array
     */
    public $runList = [];

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
        //日志
        if (!empty($crontab['log'])) {
            $this->log = $crontab['log'];
            unset($crontab['log']);
        }
        //pid存储路径
        if (!empty($crontab['pid'])) {
            $this->pidFile = $crontab['pid'];
            unset($crontab['pid']);
        }
        //临时缓存
        if (!empty($crontab['cache_data'])) {
            $this->cacheData = $crontab['cache_data'];
            unset($crontab['cache_data']);
        }
        //swoole配置信息
        if (!empty($crontab['swoole_tick'])) {
            $this->swooleTick = $crontab['swoole_tick'][0];
            $this->swooleTickMs = $crontab['swoole_tick'][1];
            unset($crontab['swoole_tick']);
        }
        $this->crontab = $crontab;
    }

    public function run()
    {
        \Co\run(function () {
            //是否使用swoole定时器
            if ($this->swooleTick) {
                $pid = \Swoole\Timer::tick($this->swooleTickMs, function ($timerId) {
                    //清理定时器
                    $log = new log($this->pidFile);
                    $pid = $log->pidRead();
                    if (empty($pid)) {
                        \Swoole\Timer::clear($timerId);
                    }
                    $this->forOne();
                });
                //写入定时pid
                $log = new  log($this->pidFile);
                $log->pidWriter($pid);
            } else {
                $this->forOne();
            }
        });
    }

    /**
     * 停止定时器运行
     */
    public function stop()
    {
        \Co\run(function () {
            $log = new log($this->pidFile);
            $pid = $log->pidRead();
            if (!empty($pid)) {
                $log->pidWriter(0);
            }
        });
    }

    /**
     * 循环定时任务
     */
    public function forOne()
    {
        for ($i = 0; $i < count($this->crontab); $i++) {
            $this->one($this->crontab[$i]);
        }
    }

    /**
     * 获取当前时间
     * @return false|string
     */
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
            if (empty($crontab)) {
                return;
            }
            $setData = new log($this->cacheData);
            $cacheData = $setData->getAppendAll();
            if (!in_array($name, $cacheData)) {
                $setData->setAppend($name);
            }
            //解析crontab 时间
            $cron = \Cron\CronExpression::factory($crontab);
            $next = $cron->getNextRunDate(date('Y-m-d H:i:s'), 0)
                ->format('Y-m-d H:i:s');
            //是否使用定时器
            if (!$this->swooleTick) {
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
            } else {
                if ($this->debug) {
                    $nowDate = date('Y-m-d H:i:s');
                    echo "{$name} while {$nowDate} {$next}..." . PHP_EOL;
                }
                if (date('Y-m-d H:i:s') != $next) {
                    return;
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
                $logs = new logs($this->log);
                $logs->write($log);
            }
            //是否显示debug输出
            if ($this->debug) {
                echo $log . PHP_EOL;
            }
            $setData->setAppendDel($name);//清理队列
        });
    }
}