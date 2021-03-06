### Log类

#### 内置Log级别方法
```php
interface LogInterface
{
    public static function emergency(string $log, $file = '', $line = 0);

    public static function alert(string $log, $file = '', $line = 0);

    public static function critical(string $log, $file = '', $line = 0);

    public static function error(string $log, $file = '', $line = 0);

    public static function warning(string $log, $file = '', $line = 0);

    public static function notice(string $log, $file = '', $line = 0);

    public static function info(string $log, $file = '', $line = 0);

    public static function debug(string $log, $file = '', $line = 0);
}
```

#### 试用示例

composer require baagee/php-log

##### 基本使用
```php
include_once __DIR__ . '/../vendor/autoload.php';
// log缓冲区占用php.ini 设置的内存百分比
$memoryLimit = 5;// 表示5%
// Log初始化 试用内置的fileLog保存到文件
\BaAGee\Log\Log::init(new \BaAGee\Log\Handler\FileLog([
    // 基本目录
    'baseLogPath'   => getcwd() . DIRECTORY_SEPARATOR . 'log',
    // 是否按照小时分割
    'autoSplitHour' => true,
    // 子目录
    'subDir'        => 'user'
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
//开启在命令行执行脚本输出Log 便于调试
\BaAGee\Log\Log::printOnStdout(true);
\BaAGee\Log\Log::notice('notice啊');
//刷新log缓冲区
\BaAGee\Log\Log::flushLogs();
\BaAGee\Log\Log::alert('alert啊');
echo 'over' . PHP_EOL;
```

##### 自定义Log保存类
自定义Log保存方式需要继承\BaAGee\Log\Base\LogHandlerAbstract

首先定义自己的Log保存处理类：
```php
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
```
然后就可以试用了
```php
// 引入composer自动类家在
include_once __DIR__.'/../vendor/autoload.php';
// 引入自定义类
include_once __DIR__.'/MyLogHandler.php';
// 初始化Log试用自定义的Log处理类
\BaAGee\Log\Log::init(new MyLogHandler(['host' => '127.0.0.1', 'port' => 9090]), 5);
// 开始试用Log
\BaAGee\Log\Log::debug('debug啊');
```
运行结果示例：
```
保存Log到mongodb
array (
  'DEBUG' => 
  array (
    0 => '[DEBUG] 2019-03-25 06:10:38 /Users/baagee/PhpstormProjects/github/Log/tests/test2.php:11 debug啊',
  ),
)%
```

##### 自定义Log字符串格式

默认的Log字符串格式：

```
// [级别] 时间 文件:行数 Log信息
[ALERT] 2019-03-14 04:02:03 /Users/baagee/PhpstormProjects/github/Log/tests/test1.php:22 alert啊
```

当不满足时可以继承`\BaAGee\Log\Base\LogFormatter`重写`getLogString`方法，返回自定义的Log格式

示例代码：

```php
include_once __DIR__ . '/../vendor/autoload.php';
// 自定义Log格式
class LogFormatter extends \BaAGee\Log\Base\LogFormatter
{
    // 重写`getLogString`方法
    protected static function getLogString($level, $log, $file, $line,$time)
    {
        return sprintf('level=%s time=%s file=%s line=%d log=%s', $level, date('Y-m-d H:i:s', $time), $file, $line, $log);
    }
}

$memoryLimit = 5;
// 传入自定义的Log格式化类名
\BaAGee\Log\Log::init(new \BaAGee\Log\Handler\FileLog([
    // 基本目录
    'baseLogPath'   => getcwd() . DIRECTORY_SEPARATOR . 'log',
    // 是否按照小时分割
    'autoSplitHour' => true,
    // 子目录
    'subDir'        => 'user'
]), $memoryLimit, LogFormatter::class);

// 其他使用方式不变
\BaAGee\Log\Log::debug('debug啊');
\BaAGee\Log\Log::info('info啊');
\BaAGee\Log\Log::notice('notice啊');
//刷新log缓冲区
\BaAGee\Log\Log::flushLogs();
\BaAGee\Log\Log::alert('alert啊');
echo 'over' . PHP_EOL;
```
###### 具体代码请查看tests目录