<?php

namespace app\common\service_client;

use think\Container;
/**
 * 执行服务
 */
class ExecuteHandle extends BaseHandle
{
    private $instance = null;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->instance = $this->getServiceInstance();
    }

    /**
     * 实例化服务对象,需要内存缓存
     */
    private function getServiceInstance()
    {
        list($category, $class, $action) = self::parsePath($this->path);
        $className = trim(str_replace('/', '\\', "$category/$class"), '\\');
        $class = 'app\\common\\service\\' . $className;
        static $objs = [];
        if (!isset($objs[$class])) {
            $objs[$class] = Container::get($class);
            $objs[$class]->setExecuter($this);
        }
        return $objs[$class];
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
            $segments = array_filter(explode('/', trim($path, " /\n\t\r\s")));
            if (count($segments) > 1) {
                $action = array_pop($segments);
                $class = array_pop($segments);
                $dir = join('/', $segments);
                $rs = [$dir, $class, $action];
            }
            $rts[$path] = $rs;
        }
        return $rts[$path];
    }

    /**
     * 执行
     */
    public function handle()
    {
        $reflectMethod = $this->getReflectMethod();
        $argv = $this->getArgv();
        return $reflectMethod->invokeArgs($this->instance, $argv);
    }

    /**
     * 读取
     */
    private function getReflectMethod()
    {
        static $rts = [];
        $path = $this->path;
        if (!isset($rts[$path])) {
            list($dir, $class, $action) = self::parsePath($this->path);
            $objService = $this->getServiceInstance();
            if (!method_exists($objService, $action)) {
                throw new \Exception("not method_exists  $action of $class ");
            }
            $rts[$path] = new \ReflectionMethod($objService, $action);
        }
        return $rts[$path];
    }

    /**
     * 解析时间参数,这个方法提供组装 用于insert
     */
    public function getArgv()
    {
        static $argv = [], $parsed = false;
        if (!$parsed) {
            $parsed = true;
            $argv = self::parseArgv($this->getReflectMethod(), $this->argv);
        }
        return $argv;
    }

    /**
     * 解析参数
     */
    private static function parseArgv(\ReflectionMethod $reflectMethod, $rawArgv)
    {
        $rt = [];//参数和默认参数整合
        $parameters = $reflectMethod->getParameters();

        // 便捷传入参数 比如getById(1)
        if (is_scalar($rawArgv) && 1 == count($parameters)) {
            $rt[key($parameters)] = $rawArgv;
        }

        if (!$rt && $parameters) {
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
        }
        return $rt;
    }
}
