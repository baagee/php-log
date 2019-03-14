<?php
/**
 * Desc: log保存base
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:23
 */

namespace BaAGee\Log\Base;
/**
 * Class HandlerBase
 * @package BaAGee\Log\Base
 */
abstract class HandlerBase
{
    /**
     * @var array 配置信息
     */
    protected static $config = [];
    /**
     * @var bool 是否初始化
     */
    protected static $isInit = false;

    /**
     * 初始化
     * @param array $config
     */
    public static function init(array $config = [])
    {
        if (static::$isInit === false) {
            if (!empty($config)) {
                static::$config = array_merge(static::$config, $config);
            }
            static::$isInit = true;
        }
    }

    /**
     * 子类需要实现的具体保存逻辑
     * @param array $logs
     * @return mixed
     */
    abstract public static function record(array $logs);
}
