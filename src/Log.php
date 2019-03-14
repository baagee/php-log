<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:17
 */

namespace BaAGee\Log;

use BaAGee\Log\Base\LogBase;
use BaAGee\Log\Base\LogInterface;

/**
 * Class Log
 * @package BaAGee\Log
 */
class Log extends LogBase implements LogInterface
{
    /**
     * Log constructor.
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    public static function alert(string $log, $file = '', $line = 0)
    {
        self::cacheLog(__FUNCTION__, $log, $file, $line);
    }

    /**
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    public static function critical(string $log, $file = '', $line = 0)
    {
        self::cacheLog(__FUNCTION__, $log, $file, $line);
    }

    /**
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    public static function debug(string $log, $file = '', $line = 0)
    {
        self::cacheLog(__FUNCTION__, $log, $file, $line);
    }

    /**
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    public static function warning(string $log, $file = '', $line = 0)
    {
        self::cacheLog(__FUNCTION__, $log, $file, $line);
    }

    /**
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    public static function error(string $log, $file = '', $line = 0)
    {
        self::cacheLog(__FUNCTION__, $log, $file, $line);
    }

    /**
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    public static function emergency(string $log, $file = '', $line = 0)
    {
        self::cacheLog(__FUNCTION__, $log, $file, $line);
    }

    /**
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    public static function notice(string $log, $file = '', $line = 0)
    {
        self::cacheLog(__FUNCTION__, $log, $file, $line);
    }

    /**
     * @param string $log
     * @param string $file
     * @param int    $line
     */
    public static function info(string $log, $file = '', $line = 0)
    {
        self::cacheLog(__FUNCTION__, $log, $file, $line);
    }
}
