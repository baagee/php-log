<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/3/25
 * Time: 下午2:03
 */
include_once __DIR__ . '/../vendor/autoload.php';

include_once __DIR__ . '/MyLogHandler.php';

\BaAGee\Log\Log::init(new MyLogHandler(['host' => '127.0.0.1', 'port' => 9090]), 5);
\BaAGee\Log\Log::debug('debug啊');