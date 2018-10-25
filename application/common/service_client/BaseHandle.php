<?php

namespace app\common\service_client;

abstract class BaseHandle
{
    protected static $log = [];//执行日志
    protected $path = '';
    protected $argv = [];
    protected $cacheLifetime = 0;
    protected $isTransOn = false;
    protected $next;

    public function __construct($config)
    {
        $this->path = $config['path'];
        $this->argv = $config['argv'];
        $this->cacheLifetime = $config['cacheLifetime'];
        $this->isTransOn = !empty($config['isTransOn']);
    }

    public function setNext($next)
    {
        $this->next = $next;
    }

    abstract public function handle();

    public function getLog()
    {
        self::$log['path'] = $this->path;
        self::$log['isTransOn'] = $this->isTransOn;
        self::$log['time_spend'] = self::getTimeSpend();
        return self::$log;
    }

    /**
     * 执行花费时间
     */
    private static function getTimeSpend()
    {
        $rt = 0;
        if (self::$log['start_time']) {
            $rt = microtime(true) - self::$log['start_time'];
        }
        return $rt;
    }

    /**
     * 记录执行时间
     */
    public function logTimeSpend()
    {
        self::$log['start_time'] = microtime(true);
    }

    /**
     * 记录是否命中缓存
     */
    protected function logReadCached($fromCached = true)
    {
        self::$log['read_cached'] = $fromCached;
    }

    /**
     * 记录实际参数,传入参数肯能是标量,也可能使用默认值
     */
    protected function logRealArgs($realArgs)
    {
        self::$log['realArgs'] = $realArgs;
    }

    /**
     * 事物回滚
     */
    protected function logTransRollback(){
        self::$log['trans_rollback'] = true;
    }
}
