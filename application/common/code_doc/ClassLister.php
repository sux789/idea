<?php

namespace app\common\code_doc;

use think\Container;

/**
 * 多级类文件读取
 */
class ClassLister
{
    /**
     * 更具命名空间或文件夹读取对应类
     * @param string|array $dirs 路径或命名空间
     * @return array
     */
    public static function handle($dirs)
    {
        $rt = [];
        foreach ((array)$dirs as $dir) {
            if ('app\\' === substr($dir, 0, 4)) {
                $dir = self::getAppPath() . '/' . str_replace('\\', '/', substr($dir, 4));
            }
            if (is_dir($dir)) {
                $rt = array_merge($rt, self::listClassFile($dir));
            }
        }
        return $rt;
    }

    /**
     * app对应path
     * @return string
     */
    private static function getAppPath()
    {
        static $rt = '';
        if (!$rt) {
            $rt = Container::get('app')->getAppPath();
        }
        return $rt;
    }

    /**
     * 读取相对路径的类
     * @param string $dir 目录,用于对服务支持分类
     * @param integer $level 目录层级,用于统计层级信息,无需客户端输入
     * @return array
     */
    public static function listClassFile($dir, $level = 1)
    {
        $rt = [];

        $objDir = dir($dir);
        while (false !== $file = $objDir->read()) {
            if ('.' == $file or '..' == $file) {
                continue;
            }
            if (is_dir("$dir/$file")) {
                $rt = array_merge($rt, self::listClassFile("$dir/$file", $level + 1));
            } elseif (strpos($file, '.php')) {
                $rt[] = self::getClassPath("$dir/$file", $level);
            }
        }
        $objDir->close();

        return $rt;
    }

    /**
     * 得到类真实路径
     * @param $file
     * @return string
     */
    private static function getClassPath($file, $level)
    {
        return str_replace([self::getAppPath(), '.php', '//', '/'], ['app', '', '\\', '\\'], $file);
    }
}