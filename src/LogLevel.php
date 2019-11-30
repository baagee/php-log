<?php
/**
 * Desc: Log级别
 * User: 01372412
 * Date: 2019/11/30
 * Time: 下午5:29
 */

namespace BaAGee\Log;
/**
 * Class LogLevel
 * @package BaAGee\Log
 */
final class LogLevel
{
    /**
     * debug 详情
     */
    const DEBUG = 'debug';
    /**
     * 重要事件
     */
    const INFO = 'info';
    /**
     * 一般性重要的事件
     */
    const NOTICE = 'notice';
    /**
     * 出现非错误性的异常
     */
    const WARNING = 'warning';
    /**
     * 运行时出现的错误，不需要立刻采取行动，但必须记录下来以备检测
     */
    const ERROR = 'error';
    /**
     * 紧急情况
     */
    const CRITICAL = 'critical';
    /**
     * 立刻采取行动
     */
    const ALERT = 'alert';
    /**
     * 系统不可用
     */
    const           EMERGENCY = 'emergency';

    /**
     * 所有的级别
     */
    protected const ALL_LEVELS = [
        self::DEBUG, self::INFO, self::NOTICE, self::WARNING,
        self::ERROR, self::CRITICAL, self::ALERT, self::EMERGENCY
    ];

    /**
     * @var array 生产环境隐藏的Log级别
     */
    protected static $productHiddenLevel = [];

    /**
     * @var array 允许的Log级别 默认是全部都允许
     */
    protected static $allowedLevels = self::ALL_LEVELS;

    /**
     * 设置生产环境隐藏的Log级别
     * @param array $levels
     */
    public static function setProductHiddenLevel(array $levels)
    {
        self::$productHiddenLevel = array_filter(array_map('strtolower', $levels), function ($v) {
            return in_array($v, self::getAllLevels());
        });
        if (!empty(self::$productHiddenLevel)) {
            // 修改允许的Log级别
            self::$allowedLevels = array_values(array_diff(self::getAllLevels(), self::$productHiddenLevel));
        }
    }

    /**
     * 返回生产环境隐藏的Log级别
     * @return array
     */
    public static function getProductHiddenLevel(): array
    {
        return self::$productHiddenLevel;
    }

    /**
     * 获取所有的Log级别
     * @return array
     */
    public static function getAllLevels()
    {
        return self::ALL_LEVELS;
    }

    /**
     * 获取环境允许的Log级别
     * @return array
     */
    public static function getAllowedLevels()
    {
        return self::$allowedLevels;
    }
}
