<?php

/**
 * Desc:
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:41
 */
include_once __DIR__ . '/../vendor/autoload.php';

$memoryLimit = 5;

\BaAGee\Log\Log::init(new \BaAGee\Log\Handler\FileLog([
    // 基本目录
    'base_log_path'   => getcwd() . DIRECTORY_SEPARATOR . 'log',
    // 是否按照小时分割
    'auto_split_hour' => true,
    // 子目录
    'sub_dir'        => 'user'
]), $memoryLimit);

$debug = false;
if ($debug == false) {
    // 设置隐藏的Log 不输出
    \BaAGee\Log\LogLevel::setProductHiddenLevel([
        \BaAGee\Log\LogLevel::DEBUG,
    ]);
}

\BaAGee\Log\Log::debug('debug啊');
\BaAGee\Log\Log::info('info啊');
\BaAGee\Log\Log::notice('notice啊');
//刷新log缓冲区
\BaAGee\Log\Log::flushLogs();
//在命令行执行脚本输出Log 便于调试
\BaAGee\Log\Log::printOnStdout(true);
\BaAGee\Log\Log::alert('alert啊');
echo 'over' . PHP_EOL;
