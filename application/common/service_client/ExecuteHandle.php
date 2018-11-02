<?php

namespace app\common\service_client;

use think\Container;

/**
 * 执行服务类
 */
class ExecuteHandle extends BaseHandle
{
    protected $instance;
    protected $baseNamespace = '';

    public function __construct($config)
    {
        parent::__construct($config);
        $this->instance = $this->getServiceInstance();
        $this->instance->setExecuter($this);//注入实际执行环境
    }

    /**
     * 实例化服务对象,需要内存缓存
     */
    private function getServiceInstance()
    {
        static $objs = [];
        $class = $this->config['class'];
        if (!isset($objs[$class])) {
            $objs[$class] = Container::get($class);
        }
        return $objs[$class];
    }


    /**
     * 执行
     */
    public function handle()
    {
        $reflectMethod = $this->getReflectMethod();
        $argv = $this->getFinalArgv();
        return $reflectMethod->invokeArgs($this->instance, $argv);
    }

    /**
     * 读取
     */
    private function getReflectMethod()
    {
        static $rts = [];
        $path = $this->path;
        $action = $this->config['action'];
        if (!isset($rts[$path])) {
            $objService = $this->getServiceInstance();
            if (!method_exists($objService, $action)) {
                throw new \Exception("not method_exists  $action of $class ");
            }
            $rts[$path] = new \ReflectionMethod($objService, $action);
        }
        return $rts[$path];
    }

    /**
     * 解析传递给服务的参数=客户端实际传递参数+代码定义默认参数
     */
    public function getFinalArgv()
    {
        static $rts = [];
        $key = $this->path;
        if ($this->argv) {
            $key .= sha1(join("\t", $this->argv));
        }

        if (!isset($rts[$key])) {
            $rts[$key] = self::parseArgv($this->getReflectMethod(), $this->argv);
        }

        return $rts[$key];
    }

    /**
     * 解析参数
     */
    private static function parseArgv(\ReflectionMethod $reflectMethod, $rawArgv)
    {
        $rt = [];//参数和默认参数整合
        $parameters = $reflectMethod->getParameters();

        // 便捷传入参数,初始化$rawArgv,比如get(1)转换为get(['name'=>1])
        if (is_scalar($rawArgv) && 1 == count($parameters)) {
            $rawArgv[$parameters[0]->getName()] = $rawArgv;
        }

        foreach ($parameters as $key => $item) {
            $var_name = $item->getName();
            if (isset($rawArgv[$var_name])) {
                $rt[$var_name] = $rawArgv[$var_name];
            } else {
                if ($item->isDefaultValueAvailable()) {
                    $rt[$var_name] = $item->getDefaultValue();
                } else {
                    $msg = "service {$reflectMethod->class} method {$reflectMethod->class} argv miss:" . $var_name;
                    throw new \InvalidArgumentException($msg);
                }
            }
        }

        return $rt;
    }
}
