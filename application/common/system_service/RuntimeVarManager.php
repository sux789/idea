<?php

namespace app\common\system_service;

/**
 * 运行时无限分层变量管理,
 * 用于运行时通信,可以根据运行时解耦开发工作
 */
class RuntimeVarManager
{
    private static $data;

    /**
     * 读取
     * key1.key2 读取对应 self::$data[key1][key2];无效key返回全部数据
     */
    public static function get($key = '')
    {
        $keys = self::key2Array($key);
        $rt = self::$data;
        if ($keys) {
            foreach ($keys as $item) {
                if (isset($rt[$item])) {
                    $rt = $rt[$item];
                } else {
                    $rt = null;
                    break;
                }
            }
        }
        return $rt;
    }

    /**
     * 把健变成有效健数组,支持0作为健
     */
    private static function key2Array($key)
    {
        $key = preg_replace(['/(\\s)+/', '/\\.+/'], ['', '.'], trim($key, ". \r\t\n"));
        return $key ? explode('.', $key) : [];
    }

    /**
     * 是否存在
     * @return boolean
     */
    public static function has($key)
    {
        $keys = self::key2Array($key);
        $data = self::$data;
        $rt = true;

        if ($keys) {
            foreach ($keys as $item) {
                if (isset($data[$item])) {
                    $data =& $data[$item];
                } else {
                    $rt = false;
                    break;
                }
            }
        }
        return $rt;
    }

    /**
     * 添加一项,不可覆盖,
     * 目的是不要随便覆盖导致混乱
     * @return boolean
     */
    public static function add($key, $val)
    {
        return self::set($key, $val, false);
    }

    /**
     * 设置
     * @param boolean $replace 是否覆盖
     * @return boolean
     */
    public static function set($key, $val, $replace = true)
    {
        $keys = self::key2Array($key);
        $lastkey = array_pop($keys);
        $parent =& self::$data;
        if ($keys) {
            foreach ($keys as $item) {
                if (!isset($parent[$item])) {
                    $parent[$item] = [];
                }
                $parent =& $parent[$item];
            }
        }

        $rt = false;
        $isKeyAvailable = !empty($lastkey);//$lastkey可用
        $isReplaceable = $replace or !isset($parent[$lastkey]);//检测是否可以覆盖
        if ($isKeyAvailable && $isReplaceable) {
            $parent[$lastkey] = $val;
            $rt = true;
        }

        return $rt;
    }
}