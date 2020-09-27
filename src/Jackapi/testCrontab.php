<?php

namespace Jackapi;
class testCrontab
{

    /**
     * 返回时间
     * @return false|string
     */
    public static function getDate()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 测试while循环
     */
    public static function whileTest()
    {
        $i = 0;
        while (true) {
            $i++;
            if ($i > 1000000) {
                break;
            }
        }
    }
}