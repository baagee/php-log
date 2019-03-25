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

```php
include_once __DIR__ . '/../vendor/autoload.php';
// log缓冲区占用php.ini 设置的内存百分比
$memoryLimit = 5;// 表示5%
// Log初始化 试用内置的fileLog保存到文件
\BaAGee\Log\Log::init($memoryLimit, \BaAGee\Log\Handler\FileLog::class, [
    // 基本目录
    'baseLogPath'   => getcwd() . DIRECTORY_SEPARATOR . 'log',
    // 是否按照小时分割
    'autoSplitHour' => true,
    // 子目录
    'subDir'        => 'user'
]);
\BaAGee\Log\Log::debug('debug啊');
\BaAGee\Log\Log::info('info啊');
\BaAGee\Log\Log::notice('notice啊');
//刷新log缓冲区
\BaAGee\Log\Log::flushLogs();
\BaAGee\Log\Log::alert('alert啊');
echo 'over' . PHP_EOL;
```

自定义Log保存方式需要继承\BaAGee\Log\Base\LogHandlerAbstract

首先定义自己的Log保存处理类：
```php
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
    // 实现record方法
    public static function record(array $logs)
    {
        print_r('保存Log到mongodb,具体的省略'.PHP_EOL);
        var_export($logs);die;
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
\BaAGee\Log\Log::init(5,MyLogHandler::class,['host'=>'127.0.0.1','port'=>9090]);
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
###### 具体代码请查看tests目录