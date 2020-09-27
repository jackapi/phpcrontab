<?php


namespace Jackapi;


class log
{
    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }


    /**
     * 日志写入
     * @param $content
     */
    public function write($content)
    {
        $fp = fopen($this->file, 'a+');
        fwrite($fp, $content);
        fclose($fp);
    }
}