<?php

namespace app\common\service_client;

/**
 * Class ServiceClient
 * @package app\common\service_client
 */
class ServiceClient
{
    /**
     * 执行调用服务,
     * 按照缓存,事物,调用链执行,每一步根据配置需要才执行
     */
    public static function handle($path = 'Index/index', $argv = [])
    {
        $cacheLifetime = self::getCacheLifetime($path);
        $isTransOn = self::isTransOn($path);
        $config = [
            'path' => $path,
            'argv' => $argv,
            'cacheLifetime' => $cacheLifetime,
        ];

        $invoker = null;// 调用执行者,肯能是缓存或事物或服务执行
        if ($cacheLifetime) {
            $invoker = new CacheHandle($config);
        }
        if ($isTransOn) {
            $transHander = new TransactionHandle($config);
            if ($invoker) {
                $invoker->setNext($transHander);
            } else {
                $invoker = $transHander;
            }
        }
        $serviceExecuter = new ExecuteHandle($config);
        if ($invoker) {
            $invoker->setNext($serviceExecuter);
        } else {
            $invoker = $serviceExecuter;
        }

        return $invoker->handle();
    }


    /**
     * 读取$path缓存时间
     * @return int
     */
    private static function getCacheLifetime($path)
    {
        $config = config("service.$path");
        return isset($config['cache_lifetime']) ? (int)$config['cache_lifetime'] : 0;
    }

    /**
     * 是否启用事物
     * @return bool
     */
    private static function isTransOn($path)
    {
        $config = config("service.$path");
        return !empty($config['trans']);
    }

}
