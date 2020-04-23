<?php
/**
 * Desc: Log格式化类
 * User: baagee
 * Date: 2019/4/3
 * Time: 上午11:04
 */

namespace BaAGee\Log\Base;
/**
 * Class LogFormatter
 * @package BaAGee\Log\Base
 */
class LogFormatter
{
    /**
     * 获取完整的log字符串
     * @param string $level 级别
     * @param string $log   log字符串
     * @param string $file  调用处文件
     * @param int    $line  调用处行数
     * @param int    $time  记录时间
     * @return string
     */
    final public static function format($level, $log, $file, $line, $time = 0)
    {
        if ($time == 0) {
            $time = microtime(true);
        }
        return static::getLogString($level, $log, $file, $line, $time);
    }

    /**
     * 获取完整的log字符串
     * @param string $level 级别
     * @param string $log   log字符串
     * @param string $file  调用处文件
     * @param int    $line  调用处行数
     * @param int    $time  记录时间
     * @return string
     */
    protected static function getLogString($level, $log, $file, $line, $time)
    {
        list($t1, $t2) = explode('.', number_format($time, 6, '.', ''));
        $time = sprintf('%s.%s', date('Y-m-d H:i:s', $t1), $t2);
        return sprintf('%s %s %s:%d %s', $level, $time, $file, $line, $log);
    }
}
