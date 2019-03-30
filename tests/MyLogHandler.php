<?php

/**
 * Desc:
 * User: baagee
 * Date: 2019/3/25
 * Time: 下午2:02
 */
class MyLogHandler extends \BaAGee\Log\Base\LogHandlerAbstract
{
    protected $config = [
        // 连接MongoDB的配置
    ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        // 连接MongoDB
        //。。。。。
    }

    public function record(array $logs)
    {
        print_r('保存Log到mongodb' . PHP_EOL);
        var_export($logs);
        die;
    }
}
