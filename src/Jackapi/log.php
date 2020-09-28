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
    public function write($content, $mode = 'a+')
    {
        $fp = fopen($this->file, $mode);
        fwrite($fp, $content);
        fclose($fp);
    }

    /**
     * 日志读取
     * @param $content
     */
    public function read($content)
    {
        $fp = fopen($this->file, 'a+');
        $result = fread($fp, filesize($this->file));
        fclose($fp);
        return $result;
    }


    /**
     * 读取缓存
     * @param $key
     * @return null
     */
    public function setAppend($value)
    {
        $result = file_get_contents($this->file);
        $result = json_decode($result);
        if (empty($result)) {
            $result[] = $value;
            $result = json_encode(
                $result
            );
            return file_put_contents($this->file, $result);
        } else {
            $result[] = $value;
            $result = json_encode(
                $result
            );
            return file_put_contents($this->file, $result);
        }
    }

    /**
     * 清理单个
     * @param $key
     * @return null
     */
    public function setAppendDel($value)
    {
        $result = file_get_contents($this->file);
        $result = json_decode($result);
        if (empty($result)) {
            return false;
        } else {
            foreach ($result as $k => $v) {
                if ($v == $value) {
                    unset($result[$k]);
                }
            }
            //重置数组信息
            $result = array_values($result);
            $result = json_encode($result);
            return file_put_contents($this->file, $result);
        }
    }

    /**
     * 读取缓存
     * @param $key
     * @return null
     */
    public function getAppendAll()
    {
        try {
            $result = file_get_contents($this->file);
            $result = json_decode($result);
            if (empty($result)) {
                return [];
            } else {
                return $result ?? [];
            }
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * 删除所有缓存
     * @param $key
     * @return null
     */
    public function getAppendDelAll()
    {
        return file_put_contents($this->file, '');
    }

    public function pidWriter($pid)
    {
        file_put_contents($this->file, $pid);
    }

    public function pidRead()
    {
        return file_get_contents($this->file);
    }
}