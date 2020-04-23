<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/4/23
 * Time: 下午2:31
 */
include __DIR__ . '/../../vendor/autoload.php';


$memoryLimit = 20;
$logPath = __DIR__ . '/..' . DIRECTORY_SEPARATOR . 'log';
// log测试
\BaAGee\Log\Log::init(new \BaAGee\Log\Handler\FileLog([
    // 基本目录
    'baseLogPath' => $logPath,
    // 是否按照小时分割
    'autoSplitHour' => true,
    // 子目录
    'subDir' => 'ben'
]), $memoryLimit, \BaAGee\Log\Base\LogFormatter::class, true, false);
SeasLog::setBasePath($logPath);

$s = microtime(true);
for ($i = 0; $i <= 10000; $i++) {
    \BaAGee\Log\Log::info('info啊');
}
$e = microtime(true);
echo 'MyLog TIME:' . ($e - $s) * 1000;

// seasLog测试
$s = microtime(true);
for ($i = 0; $i <= 10000; $i++) {
    SeasLog::info('info啊');
}
$e = microtime(true);
echo PHP_EOL . 'SeasLog TIME:' . ($e - $s) * 1000;

echo PHP_EOL . 'over' . PHP_EOL;


