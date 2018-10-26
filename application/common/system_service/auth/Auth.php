<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/26
 */

namespace app\common\system_service\auth;
/**
 * 类 登录认证
 * 使用:登录成功createToken设置或返回token,读取登录信息get
 * @package app\common\system_service\auth
 */
class Auth
{

    private static $ins;

    /**
     * 判断是否登录
     * @return bool
     */
    public static function isLogin()
    {
        return !!self::get();
    }

    /**
     * 读取登录信息
     * @return mixed
     */
    public static function get()
    {
        return self::authInit() ? self::$ins->get() : [];

    }

    /**
     * 自动初始化
     */
    private static function authInit()
    {
        if (!self::$ins) {
            $type = '';
            $item = config('app.auth_type');
            if (is_string($item)) {
                $type = $item;
            } elseif (is_callable($item)) {
                $type = $item();
            }
            $type = strtolower($type);

            $field_name = config('app.auth_field_name');
            self::init($type, $field_name);
        }
        return self::$ins;
    }

    /**
     * 得到例化
     */
    public static function init($type, $storeName = 'token')
    {
        $argv = ['type' => $type, 'storeName' => $storeName ? $storeName : 'token'];
        return self::$ins = container()->get(self::class . ucfirst($type), $argv);
    }

    /**
     * 创建token,PC端在建立对应cookie
     * @return string
     */
    public static function createToken($userInfo, $lifetime = 0)
    {
        return self::authInit() ? self::$ins->createToken($userInfo, $lifetime) : '';
    }
}