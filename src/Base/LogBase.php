<?php
/**
 * Desc: Log基础类
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:16
 */

namespace BaAGee\Log\Base;

/**
 * Class LogBase
 * @package BaAGee\Log\Base
 */
abstract class LogBase
{
    use ProhibitNewClone;
    /**
     * @var bool 是否初始化Log
     */
    protected static $isInit = false;

    /**
     * @var string 保存Log的处理类 默认文件保存
     */
    protected static $handler = \BaAGee\Log\Handler\FileLog::class;

    /**
     * @var array 缓存的Log信息
     */
    protected static $logs = [];

    /**
     * @var int log缓存区大小
     */
    protected static $memoryLimit = 0;

    /**
     * @var int
     */
    protected static $currentLogSize = 0;

    /**
     * log初始化
     * @param int    $memoryLimitPercent log缓存占用PHP最大内存百分比 默认20 大小限制：>0 && <90
     * @param string $handler            保存Log的处理类
     * @param array  $handlerConfig      保存Log的处理类初始化的参数
     */
    public static function init(int $memoryLimitPercent = 20, string $handler = '', array $handlerConfig = [])
    {
        if (self::$isInit === false) {
            if ($memoryLimitPercent < 0 || $memoryLimitPercent > 90) {
                // 如果百分比小于0 或者大于90% 就使用20%
                $memoryLimitPercent = 20;
            }
            self::$memoryLimit = self::getMemoryLimit($memoryLimitPercent);

            if (!empty($handler)) {
                self::$handler = $handler;
            }
            call_user_func(self::$handler . '::init', $handlerConfig);
            register_shutdown_function(self::class . '::commitLogs');
            self::$isInit = true;
        }
    }

    /**
     * 获取缓冲区大小
     * @param $memoryLimitPercent
     * @return int
     */
    private static function getMemoryLimit($memoryLimitPercent)
    {
        $memoryPercent  = $memoryLimitPercent / 100;
        $IniMemoryLimit = ini_get('memory_limit');
        if ('M' == substr($IniMemoryLimit, -1)) {
            $IniMemoryLimit = substr($IniMemoryLimit, 0, strlen($IniMemoryLimit) - 1) * 1024 * 1024;
        }
        return intval(intval($IniMemoryLimit) * $memoryPercent);
    }

    /**
     * 获取完整的log字符串
     * @param string $level 级别
     * @param string $log   log字符串
     * @param string $file  调用处文件
     * @param int    $line  调用处行数
     * @return string
     */
    private static function getLogString(string $level, string $log, $file = '', $line = 0)
    {
        if ($file == '' || $line == 0) {
            list($file, $line) = self::getCallFileLine();
        }
        // level  time  file:line log
        return sprintf('[%s] %s %s:%d %s', $level, date('Y-m-d H:i:s'), $file, $line, $log);
    }

    /**
     * 获取调用Log的文件和行数
     * @return array
     */
    private static function getCallFileLine()
    {
        if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        } else {
            $backtrace = debug_backtrace();
        }
        $call = $backtrace[3];
        return [$call['file'], $call['line']];
    }

    /**
     * 中途刷新Log缓冲区保存
     */
    public static function flushLogs()
    {
        call_user_func(self::$handler . '::record', self::$logs);
        self::reset();
    }

    /**
     * 重置Log相关变量
     */
    private static function reset()
    {
        self::$currentLogSize = 0;
        self::$logs           = [];
    }

    /**
     * 最后提交保存Log
     */
    public static function commitLogs()
    {
        if (function_exists('fastcgi_finish_request')) {
            //响应完成, 关闭连接 ,以后的输出和报错不会显示
            fastcgi_finish_request();
        }
        self::flushLogs();
    }

    /**
     * 缓存Log字符串
     * @param string $level 级别
     * @param string $log   log字符串
     * @param string $file  调用处文件
     * @param int    $line  调用处行数
     */
    protected static function cacheLog(string $level, string $log, $file = '', $line = 0)
    {
        if (self::isOutOfMemory()) {
            self::flushLogs();
        }
        $level                = strtoupper($level);
        $logString            = self::getLogString($level, $log, $file, $line);
        self::$currentLogSize += strlen($logString);
        self::$logs[$level][] = $logString;
    }

    /**
     * 判断log大小是否到达log缓存区
     * @return bool
     */
    private static function isOutOfMemory()
    {
        // 当前log的缓存大小超过了memory_limit限制的百分之X时，超出缓存
        return self::$currentLogSize >= self::$memoryLimit;
    }
}
