<?php
/**
 * Desc: 文件保存Log信息类
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:15
 */

namespace BaAGee\Log\Handler;

use BaAGee\Log\Base\HandlerBase;

/**
 * Class FileLog
 * @package BaAGee\Log\Handler
 */
class FileLog extends HandlerBase
{
    /**
     * @var array 配置
     */
    protected static $config = [
        // 基本目录
        'baseLogPath'   => '',
        // 是否按照小时分割
        'autoSplitHour' => true,
        // 子目录
        'subDir'        => ''
    ];

    /**
     * 初始化
     * @param array $config
     */
    public static function init(array $config = [])
    {
        parent::init($config);
        if (empty(self::$config['baseLogPath'])) {
            self::$config['baseLogPath'] = getcwd() . DIRECTORY_SEPARATOR . 'log';
        }
    }

    /**
     * 记录Log信息
     * @param array $logs
     *                    [
     *                    "level"=>[
     *                    "abc",
     *                    "def",
     *                    ],
     *                    ]
     * @throws \Exception
     */
    public static function record(array $logs)
    {
        foreach ($logs as $level => $logArray) {
            $logFileName = (self::$config['autoSplitHour'] ? date('H_') : '') . $level . '.log';
            $logFile     = implode(DIRECTORY_SEPARATOR,
                array_filter([self::$config['baseLogPath'], self::$config['subDir'], date('Y_m_d'), $logFileName])
            );
            self::makeDir(dirname($logFile));
            file_put_contents($logFile, implode(PHP_EOL, $logArray) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * 创建log目录
     * @param $path
     * @throws \Exception
     */
    protected static function makeDir($path)
    {
        if (!is_dir($path) || !is_writeable($path)) {
            if (!@mkdir($path, 0755, true)) {
                throw new \Exception('创建文件夹【' . $path . '】失败');
            }
        }
    }
}
