<?php
/**
 * Desc: Log基础类
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:16
 */

namespace BaAGee\Log\Base;

use BaAGee\Log\LogLevel;

/**
 * Class LogAbstract
 * @package BaAGee\Log\Base
 */
abstract class LogAbstract
{
    use ProhibitNewClone;
    /**
     * @var bool cli模式下是否在命令行下输出Log
     */
    protected static $printOnStdout = false;
    /**
     * @var bool 是否初始化Log
     */
    protected static $isInit = false;
    /**
     * @var bool Cli下是否缓存
     */
    protected static $cliCache = false;

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
     * 是否在命令行下运行脚本直接输出Log
     * @param bool $open
     * @return bool
     */
    final public static function printOnStdout(bool $open)
    {
        //只有在命令行下运行脚本才会输出Log
        if (PHP_SAPI == 'cli') {
            self::$printOnStdout = $open;
        } else {
            return false;
        }
    }

    /**
     * log初始化
     * @param int                $memoryLimitPercent log缓存占用PHP最大内存百分比 默认20 大小限制：>0 && <90
     * @param LogHandlerAbstract $handler            保存Log的处理类
     * @param string             $formatter          Log格式化类
     * @param bool               $cliCache           cli下是否缓存
     * @param bool               $printOnStdout      cli下是否直接输出Log
     * @throws \Exception
     */
    public static function init(LogHandlerAbstract $handler, int $memoryLimitPercent = 20, $formatter = LogFormatter::class,
                                bool $cliCache = false, bool $printOnStdout = false)
    {
        if (self::$isInit === false) {
            if ($memoryLimitPercent < 0 || $memoryLimitPercent > 90) {
                // 如果百分比小于0 或者大于90% 就使用20%
                $memoryLimitPercent = 20;
            }
            self::$memoryLimit   = self::getMemoryLimit($memoryLimitPercent);
            self::$cliCache      = $cliCache;
            self::$printOnStdout = $printOnStdout;
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
        $logArray = [];
        foreach (self::$logs as $level => $logArr) {
            foreach ($logArr as $log) {
                $logArray[$level][] = self::$logFormatter::format($level, $log['log'], $log['file'], $log['line'], $log['time']);
            }
        }

        call_user_func([self::$handler, 'record'], $logArray);
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
     * 获取调用Log的文件和行数
     * @return array
     */
    final private static function getLogCallFileLine()
    {
        if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        } else {
            $backtrace = debug_backtrace();
        }
        $call = $backtrace[2] ?? [];
        return [$call['file'] ?? '', $call['line'] ?? 0];
    }

    /**
     * 缓存Log字符串
     * @param string $level 级别
     * @param string $log   log字符串
     * @param string $file  调用处文件
     * @param int    $line  调用处行数
     */
    protected static function saveLog(string $level, string $log, $file = '', $line = 0)
    {
        if (empty($file) || empty($line)) {
            list($file, $line) = self::getLogCallFileLine();
        }
        $level = strtoupper($level);
        // $logString = self::$logFormatter::format($level, $log, $file, $line);

        $logInfo = [
            // 'level' => $level,
            'log'  => $log,
            'time' => microtime(true),
            'file' => $file,
            'line' => $line
        ];
        if (PHP_SAPI == 'cli' && self::$cliCache === false) {
            // 命令行模式下不缓存时 实时保存
            // self::$logs[$level][] = $logString;

            self::$logs[$level][] = $logInfo;
            self::flushLogs();
        } else {
            // cgi模式下判断是否超过缓冲区，满了就刷新保存，否则暂存缓冲区
            if (self::isOutOfMemory()) {
                self::flushLogs();
            }
            // 2倍模拟
            self::$currentLogSize += strlen($log) * 2;
            self::$logs[$level][] = $logInfo;

            // self::$logs[$level][] = $logString;
        }
        if (PHP_SAPI == 'cli' && self::$printOnStdout) {
            self::printCliLog($level, $log, $file, $line);
        }
    }

    /**
     * cli下直接输出Log
     * @param string $level
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    private static function printCliLog(string $level, $log, string $file, $line)
    {
        $str   = trim(self::$logFormatter::format($level, $log, $file, $line));
        $level = strtolower($level);
        //给点颜色看看
        if (in_array($level, [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE,])) {
            //浅绿
            echo sprintf("\033[0;32m%s\033[0m", $str);
        } elseif (in_array($level, [LogLevel::WARNING, LogLevel::ERROR,])) {
            // 黄
            echo sprintf("\e[33;33m%s\e[0m", $str);
        } elseif (in_array($level, [LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY,])) {
            // 红
            echo sprintf("\e[31;31m%s\e[0m", $str);
        } else {
            echo $str;
        }
        echo PHP_EOL;
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
