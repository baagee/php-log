<?php

/**
 * Desc:
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:41
 */
include_once __DIR__ . '/../vendor/autoload.php';

class LogFormatter extends \BaAGee\Log\Base\LogFormatter
{
    protected static function getLogString($level, $log, $file, $line, $time)
    {
        return sprintf('level=%s time=%s file=%s line=%d log=%s', $level, date('Y-m-d H:i:s', $time), $file, $line, $log);
    }
}

$memoryLimit = 5;

\BaAGee\Log\Log::init(new \BaAGee\Log\Handler\FileLog([
    // 基本目录
    'base_log_path'   => getcwd() . DIRECTORY_SEPARATOR . 'log',
    // 是否按照小时分割
    'auto_split_hour' => true,
    // 子目录
    'sub_dir'        => 'user'
]), $memoryLimit, LogFormatter::class);
//在命令行执行脚本输出Log 便于调试
\BaAGee\Log\Log::printOnStdout(true);
\BaAGee\Log\Log::debug('debug啊');
\BaAGee\Log\Log::info('info啊');
\BaAGee\Log\Log::notice('notice啊');
//刷新log缓冲区
\BaAGee\Log\Log::flushLogs();
\BaAGee\Log\Log::warning("warning 啊");
\BaAGee\Log\Log::error("error 啊");

\BaAGee\Log\Log::critical("critical 啊");
\BaAGee\Log\Log::alert('alert啊');
\BaAGee\Log\Log::emergency("emergency 啊");
echo 'over' . PHP_EOL;
