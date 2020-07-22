<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/9/20
 * Time: 17:43
 */

use BaAGee\Log\Log;

include __DIR__ . '/../vendor/autoload.php';


$memoryLimit = 5;

\BaAGee\Log\Log::init(new \BaAGee\Log\Handler\FileLog([
    // 基本目录
    'baseLogPath' => getcwd() . DIRECTORY_SEPARATOR . 'log',
    // 是否按照小时分割
    'autoSplitHour' => true,
    // 子目录
    'subDir' => 'user'
]), $memoryLimit);
Log::listenOnWrite(function ($level, $logArr) {
    $logArr['time'] = microtime(true);
    echo sprintf('%s: %s' . PHP_EOL, $level, json_encode($logArr, JSON_UNESCAPED_UNICODE));
});
Log::listenOnWrite(function ($level, $logArr) {
    $logArr['time'] = time();
    echo sprintf('%s: %s' . PHP_EOL, $level, json_encode($logArr, JSON_UNESCAPED_UNICODE));
    return $logArr;
});
\BaAGee\Log\Log::debug('debug啊');
\BaAGee\Log\Log::info('info啊');
\BaAGee\Log\Log::notice('notice啊');
\BaAGee\Log\Log::alert('alert啊');
\BaAGee\Log\Log::warning('warning');

echo 'over' . PHP_EOL;
