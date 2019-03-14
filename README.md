### Log类

#### 内置方法
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
