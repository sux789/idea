<?php

namespace app\common\service_client;

/**
 * Class ServiceClient
 * @package app\common\service_client
 */
class ServiceClient
{
    /**
     * handle
     * @param string $path 服务路径dir/class/method
     * @param array $argv 调用参数
     * @return mixed|null
     */
    public static function handle($path = 'Index/index', $argv = [])
    {
        list($class, $action, $path) = self::parsePath($path);
        $config = config("service.$path");

        $config['cacheLifetime'] = $config['cacheLifetime'] ?? 0;
        $config['isTransOn'] = $config['isTransOn'] ?? false;
        $config['argv'] = $argv;

        $config['path'] = $path;
        $config['class'] = $class;
        $config['action'] = $action;

        $invoker = self::getInvoker($config);
        return $invoker->handle();
    }

    /**
     * 解析路径dir/serivce_class/action,用于实例化类和执行action
     * @return array []|[$dir, $class, $action]
     */
    public static function parsePath($path)
    {
        static $rts = [];
        if (!isset($rts[$path])) {
            $rs = [];
            $segments = array_filter(explode('/', trim($path, " /\n\t\r")));
            if (count($segments) > 1) {
                $action = array_pop($segments);
                $className = classname(array_pop($segments));
                $namespace = config('service_base_namespace');
                $class = '';//::class
                $newPath = '';// 重新格式化路径,整个系统调用一致
                if ($segments) {
                    $class = "$namespace\\" . join('\\', $segments) . "\\$className";
                    $newPath = join('/', $segments) . "$className/$action";
                } else {
                    $class = config('service_base_namespace') . "\\$className";
                    $newPath = "$className/$action";
                }
                $rs = [$class, $action, $newPath];
            }
            $rts[$path] = $rs;
        }
        return $rts[$path];
    }

    /**
     * 得到调用对象,可能是缓存|事物|服务执行器之一
     * @param array $config
     * @return CacheHandle|ExecuteHandle|TransactionHandle
     */
    static function getInvoker(array $config)
    {
        $invoker = null;// 调用处理器,肯能是缓存或事物或服务执行
        if ($config['cacheLifetime']) {
            $invoker = new CacheHandle($config);
        }
        if ($config['isTransOn']) {
            $invoker = new TransactionHandle($config);
        }
        $serviceExecuter = new ExecuteHandle($config);
        if ($invoker) {
            $invoker->setNext($serviceExecuter);
        } else {
            $invoker = $serviceExecuter;
        }
        return $invoker;
    }
}
