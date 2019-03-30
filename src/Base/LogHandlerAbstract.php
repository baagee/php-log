<?php
/**
 * Desc: log保存base
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:23
 */

namespace BaAGee\Log\Base;

/**
 * 具体保存log的逻辑需要继承此类 实现record方法
 * Class LogHandlerAbstract
 * @package BaAGee\Log\Base
 */
abstract class LogHandlerAbstract
{
    /**
     * @var array 配置信息
     */
    protected $config = [];

    /**
     * LogHandlerAbstract constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 子类需要实现的具体保存逻辑
     * @param array $logs
     * @return mixed
     */
    abstract public function record(array $logs);
}
