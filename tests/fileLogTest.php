<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/7/27
 * Time: 20:39
 */

include __DIR__ . '/../vendor/autoload.php';


class fileLogTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $memoryLimit = 50;
        \BaAGee\Log\Log::init(new \BaAGee\Log\Handler\FileLog([
            // 基本目录
            'baseLogPath' => getcwd() . DIRECTORY_SEPARATOR . 'log',
            // 是否按照小时分割
            'autoSplitHour' => true,
            // 子目录
            'subDir' => 'user'
        ]), $memoryLimit);
    }

    public function testAAA()
    {
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
        \BaAGee\Log\Log::printOnStdout(false);
        for ($i = 0; $i < 100; $i++) {
            \BaAGee\Log\Log::debug('debug啊');
            \BaAGee\Log\Log::info('info啊');
            \BaAGee\Log\Log::notice('notice啊');
            \BaAGee\Log\Log::commitLogs();
        }
        $this->assertEquals(1, 1);
    }
}
