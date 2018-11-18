<?php

namespace app\common\service_client;

/**
 * 服务调用客户端类
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

        $config['cachekeyHandle'] = $config['cachekeyHandle'] ?? '';//自定义缓存处理器
        $config['cacheLifetime'] = $config['cacheLifetime'] ?? 0;//缓存时间,0不缓存
        $config['isTransOn'] = $config['isTransOn'] ?? false;//是否启用事物
        $config['argv'] = $argv;//调用参数
        $config['path'] = $path;//服务标志,用于缓存等
        $config['class'] = $class;//当前执行的服务完整类名
        $config['action'] = $action;//当前执行服务方法

        $invoker = self::getInvoker($config);
        return $invoker->handle();
    }

    /**
     * 解析路径dir/serivce_class/action,用于实例化类和执行action
     * @return array []|[$class, $action, $path]
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
                $class = '';//完整类名
                $newPath = '';//格式化的路径,客户端路径支持小写;
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
        // 如果有缓存时间
        if ($config['cacheLifetime']) {
            $invoker = new CacheHandle($config);
        }
        // 如果有事物处理
        if ($config['isTransOn']) {
            $transactionHandle = new TransactionHandle($config);
            if ($invoker) {
                $invoker->setNext($transactionHandle);
            } else {
                $invoker = $transactionHandle;
            }
        }
        //服务执行对象
        $serviceExecuter = new ExecuteHandle($config);
        if ($invoker) {
            $invoker->setNext($serviceExecuter);
        } else {
            $invoker = $serviceExecuter;
        }
        return $invoker;
    }
}
