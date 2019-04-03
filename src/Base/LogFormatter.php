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
     * @return string
     */
    final public static function format($level, $log, $file = '', $line = 0)
    {
        if ($file == '' || $line == 0) {
            list($file, $line) = self::getLogCallFileLine();
        }
        return static::getLogString($level, $log, $file, $line);
    }

    /**
     * 获取完整的log字符串
     * @param string $level 级别
     * @param string $log   log字符串
     * @param string $file  调用处文件
     * @param int    $line  调用处行数
     * @return string
     */
    protected static function getLogString($level, $log, $file, $line)
    {
        return sprintf('%s %s %s:%d %s', $level, date('Y-m-d H:i:s'), $file, $line, $log);
    }

    /**
     * 获取调用Log的文件和行数
     * @return array
     */
    final private static function getLogCallFileLine()
    {
        if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        } else {
            $backtrace = debug_backtrace();
        }
        $call = $backtrace[3];
        return [$call['file'], $call['line']];
    }
}
