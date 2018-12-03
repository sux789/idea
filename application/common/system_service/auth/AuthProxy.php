<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/26
 */

namespace app\common\system_service\auth;

/**
 * 身份认证
 * 使用:登录成功createToken设置或返回token,读取登录信息get
 * @package app\common\system_service\auth
 */
class AuthProxy
{
    const TYPE_PC = 'pc';
    const TYPE_ADMIN = 'admin';
    const TYPE_MOBILE = 'mobile';

    private $ins;

    /**
     * 创建token,PC端在建立对应cookie
     * @return string
     */
    public function createToken($userInfo, $lifetime = 0)
    {
        return $this->authInit() ? $this->ins->createToken($userInfo, $lifetime) : '';
    }

    /**
     * 自动初始化
     */
    private function authInit()
    {
        if (!$this->ins) {
            $type = '';
            $item = config('app.auth_type');
            if (is_string($item)) {
                $type = $item;
            } elseif (is_callable($item)) {
                $type = $item();
            }
            $type = strtolower($type);

            $field_name = config('app.auth_field_name');
            $this->init($type, $field_name);
        }
        return $this->ins;
    }

    /**
     * 得到类型实例
     */
    public function init($type, $storeName = 'token')
    {
        $argv = ['type' => $type, 'storeName' => $storeName ? $storeName : 'token'];
        $class=self::getNamespace().'\\Auth'.ucfirst($type);
        $this->ins = container()->get($class, $argv);
        return $this;
    }

    /**
     * 读取登录验证信息
     * @return mixed
     */
    public function get()
    {
        return $this->authInit() ? $this->ins->get() : [];

    }

    /**
     * 清除登录验证信息
     * @return bool
     */
    public function delete()
    {
        return $this->authInit() ? $this->ins->delete() : false;
    }

    /**
     * 自己命名空间
     * @return bool|string
     */
    private static function getNamespace(){
        return  substr(self::class,0,strrpos(self::class,'\\'));;
    }
}