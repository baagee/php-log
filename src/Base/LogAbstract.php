<?php
/**
 * Desc: Log基础类
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:16
 */

namespace BaAGee\Log\Base;

/**
 * Class LogAbstract
 * @package BaAGee\Log\Base
 */
abstract class LogAbstract
{
    use ProhibitNewClone;
    /**
     * @var bool 是否初始化Log
     */
    protected static $isInit = false;

    /**
     * @var string 保存Log的处理类 默认文件保存
     */
    protected static $handler = null;

    /**
     * @var LogFormatter
     */
    protected static $logFormatter = LogFormatter::class;

    /**
     * @var array 缓存的Log信息
     */
    protected static $logs = [];

    /**
     * @var int log缓存区大小
     */
    protected static $memoryLimit = 0;

    /**
     * @var int 当前缓冲区Log大小
     */
    protected static $currentLogSize = 0;

    /**
     * log初始化
     * @param int                $memoryLimitPercent log缓存占用PHP最大内存百分比 默认20 大小限制：>0 && <90
     * @param LogHandlerAbstract $handler            保存Log的处理类
     * @param string             $formatter          Log格式化类
     * @throws \Exception
     */
    public static function init(LogHandlerAbstract $handler, int $memoryLimitPercent = 20, $formatter = LogFormatter::class)
    {
        if (self::$isInit === false) {
            if ($memoryLimitPercent < 0 || $memoryLimitPercent > 90) {
                // 如果百分比小于0 或者大于90% 就使用20%
                $memoryLimitPercent = 20;
            }
            self::$memoryLimit = self::getMemoryLimit($memoryLimitPercent);

            if (!empty($handler)) {
                if (is_subclass_of($handler, LogHandlerAbstract::class)) {
                    self::$handler = $handler;
                } else {
                    throw new \Exception($handler . '没有继承' . LogHandlerAbstract::class);
                }
            }
            if (!empty($formatter)) {
                if ($formatter === LogFormatter::class || is_subclass_of($formatter, LogFormatter::class)) {
                    self::$logFormatter = $formatter;
                } else {
                    throw new \Exception(sprintf('%s没有继承%s', $formatter, LogFormatter::class));
                }
            }
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
     * 中途刷新Log缓冲区保存
     */
    public static function flushLogs()
    {
        call_user_func([self::$handler, 'record'], self::$logs);
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
        try {
            if (function_exists('fastcgi_finish_request')) {
                //响应完成, 关闭连接 ,以后的输出和报错不会显示
                fastcgi_finish_request();
            }
            self::flushLogs();
        } catch (\Throwable $e) {
            // 捕获所有抛出的错误 保证不会影响到后面的register_shutdown_function
            // TODO something
        }
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
        $logString            = self::$logFormatter::format($level, $log, $file, $line);
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
