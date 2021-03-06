<?php
/**
 * Desc: 文件保存Log信息类
 * User: baagee
 * Date: 2019/3/14
 * Time: 上午10:15
 */

namespace BaAGee\Log\Handler;

use BaAGee\Log\Base\LogHandlerAbstract;

/**
 * Class FileLog
 * @package BaAGee\Log\Handler
 */
class FileLog extends LogHandlerAbstract
{
    /**
     * @var array 配置
     */
    protected $config = [
        // 基本目录
        'base_log_path'   => '',
        // 是否按照小时分割
        'auto_split_hour' => true,
        // 子目录
        'sub_dir'        => ''
    ];

    /**
     * 初始化
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        if (empty($this->config['base_log_path'])) {
            $this->config['base_log_path'] = getcwd() . DIRECTORY_SEPARATOR . 'log';
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
    public function record(array $logs)
    {
        foreach ($logs as $level => $logArray) {
            $logFileName = ($this->config['auto_split_hour'] ? date('H_') : '') . $level . '.log';
            $logFile     = implode(DIRECTORY_SEPARATOR,
                array_filter([$this->config['base_log_path'], $this->config['sub_dir'], date('Y_m_d'), $logFileName])
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
