<?php

namespace app\common\code_doc;

use think\Container;
use think\facade\Cache;

/**
 * 类 解析服务
 * 协调缓存,类列表器,类解析器,提供解析数据
 * @package app\common\code_doc
 */
class ParseServer
{
    const TYPE_CONTROLLER = 'controller';
    const TYPE_SERVICE = 'service';
    const TYPE_SCHEMA = 'schema';

    /**
     * clear缓存
     */
    public static function clear()
    {
        self::getCacheServer()->clear();
    }

    /**
     * 获取缓存引擎,所以特别设置,业务不实用File引擎
     * @return \think\cache\Driver
     */
    private static function getCacheServer()
    {
        static $rt;
        if (!$rt) {
            $options = [
                'type' => 'File',
                'expire' => 0,
                'prefix' => 'code_doc',
                'path' => '../runtime/cache/',
            ];

            $rt = Cache:: connect($options);
        }
        return $rt;
    }

    /**
     * 读取服务信息
     */
    public static function listParsedService()
    {
        return self::listPrasedResut('service');
    }

    /**
     * 读取解析结果
     * @param string $type
     * @return mixed
     */
    private static function listPrasedResut($type)
    {
        $behaverMaps = self::listBehaverMapping();
        $behaverMap = $behaverMaps[$type];

        list($cacheKey, $action, $argv) = $behaverMap;

        $rt = self::getCacheServer()->get($cacheKey);
        if (!$rt) {
            $rt = call_user_func_array($action, $argv);
            self::getCacheServer()->set($cacheKey, $rt);
        }
        return $rt;
    }

    /**
     * 读取行为对应列表
     * 缓存和执行action参数对应表,便于管理,便于统一处理
     * @return array [type=>cacheKey,callableAction,argv]]
     */
    private static function listBehaverMapping()
    {
        $paths = self::listPathConfig();
        $rt = [
            self::TYPE_CONTROLLER => [
                'parsed_controller', 'self::parseClass', [$paths[self::TYPE_CONTROLLER]]
            ],
            self::TYPE_SERVICE => [
                'parsed_service', 'self::parseClass', [$paths[self::TYPE_SERVICE]]
            ],
            self::TYPE_SCHEMA => [
                'parsed_schema', [DatabaseSchema::class, 'handle'], []
            ],
        ];
        return $rt;
    }

    /**
     * 读取路径配置列表
     * @todo 应从配置或参数传递,或遍历目录读取
     * @return array
     */
    private static function listPathConfig()
    {
        return [
            'service' => 'app\common\service',
            'controller' => [
                'app\index\controller',
                'app\admin\controller',
            ]
        ];
    }

    /**
     * 读取数据库结构列表
     */
    public static function listSchema()
    {
        return self::listPrasedResut('schema');
    }

    /**
     * 读取相服务关联的控制器方法
     * @return array
     */
    public static function listRelatedAction()
    {
        $rt = [];
        foreach (self::listParsedController() as $class => $classInfo) {
            $classPath = str_replace(['app\\', '\\controller\\'], ['', '/'], $class);
            foreach ($classInfo['methods'] as $methodName => $method) {
                $calledService = $classInfo['called_service'][$methodName] ?? [];
                foreach ($calledService as $service) {
                    $rt[$service][] = $classPath . '/' . $methodName;
                }
            }
        }
        return $rt;
    }

    /**
     * 读取控制信息
     */
    public static function listParsedController()
    {
        return self::listPrasedResut('controller');
    }

    /**
     * 解析类
     * @param $dirs 目录或命名空间
     * @return array
     * @throws \ReflectionException
     */
    private static function parseClass($dirs)
    {
        $classes = ClassLister::handle($dirs);
        $rt = [];
        foreach ($classes as $class) {
            $obj = Container::get($class);
            $rt[$class] = ClassParser::handle((new \ReflectionClass($obj)));
        }
        return $rt;
    }
}