<?php

namespace app\common\service_client;

use think\facade\Cache;
use app\common\service_client\BaseHandle;
/**
 * 对路径和参数为键缓存服务执行结果
 */
class CacheHandle extends BaseHandle
{

    public function handle()
    {
        $hasCached = true; //把读缓存和判断是否缓存分开,减少一次检查缓存是否存在
        $key = self::getCacheKey($this->path, $this->argv);
        $rt = self::getByCache($key);

        if (!$rt) {
            $hasCached = self::hasCached($key);
        }

        if (!$hasCached) {
            $rt = $this->next->handle();
            self::setCache($key, $rt, $this->cacheLifetime);
        }
        return $rt;
    }

    /**
     * 得到缓存key
     */
    private static function getCacheKey($path, $argv)
    {
        ksort($argv, SORT_STRING);//一致参数固定顺序
        $sha1 = sha1(join('%&^`|"/', $argv), true);//不同参数有不同sha1,比如[10,1]与[1,01]
        return $path . '/' . $sha1;//缓存key可读
    }

    /**
     * 读缓存
     */
    private static function getByCache($key)
    {
        return Cache::get($key);
    }

    /**
     * 是否已经缓存
     */
    private static function hasCached($key)
    {
        return Cache::has($key);
    }

    /**
     * 设置缓存
     */
    private static function setCache($key, $value, $cacheLifetime)
    {
        return \cache($key, $value, $cacheLifetime);
    }

}
