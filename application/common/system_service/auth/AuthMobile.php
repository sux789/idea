<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/26
 */

namespace app\common\system_service\auth;

class AuthMobile extends AuthBase
{
    public function getToken()
    {
        return input("get.{$this->storeName}");
    }

    public function createToken($userInfo, $lifetime = 0)
    {
        return self::encode($userInfo, $lifetime);
    }
}