<?php

namespace app\common\service_client;

use think\facade\Cache;

/**
 * 服务结果缓存类
 * 对路径和参数为键缓存服务执行结果
 * @todo 提供注入缓存键处理器,缓存键可能是数组用于支持集合,但还没处理
 */
class CacheHandle extends BaseHandle
{
    /**
     * handle
     * @return mixed
     */
    public function handle()
    {
        $hasCached = true; //把读缓存和判断是否缓存分开,减少一次检查缓存是否存在
        $key = $this->getCacheKey();
        $rt = self::getByCache($key);
        if (!$rt) {
            $hasCached = self::hasCached($key);// 避免特殊情况缓存0,''等
        }
        if (!$hasCached) {
            $rt = $this->next->handle();
            self::setCache($key, $rt, $this->config['cacheLifetime']);
        }
        return $rt;
    }

    /**
     * 读取缓存key
     * @return mixed
     */
    function getCacheKey()
    {
        $keyHandel = is_callable($this->config['cachekeyHandle'])
            ? $this->config['cachekeyHandle']
            : 'get_cache_key';
        return call_user_func_array($keyHandel, [$this->path, $this->argv]);
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
