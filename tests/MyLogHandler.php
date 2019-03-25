<?php

/**
 * Desc:
 * User: baagee
 * Date: 2019/3/25
 * Time: 下午2:02
 */
class MyLogHandler extends \BaAGee\Log\Base\LogHandlerAbstract
{
    protected static $config=[
        // 连接MongoDB的配置
    ];
    public static function init(array $config=[]){
        parent::init($config);
        // 连接MongoDB
        //。。。。。
    }
    public static function record(array $logs)
    {
        print_r('保存Log到mongodb'.PHP_EOL);
        var_export($logs);die;
    }
}
