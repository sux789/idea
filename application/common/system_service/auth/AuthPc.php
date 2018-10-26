<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/26
 */

namespace app\common\system_service\auth;


class AuthPc extends AuthBase
{
    function getToken()
    {
        return \cookie($this->storeName);
    }

    /**
     * @param array $userInfo 用户信息
     * @param int $lifetime 过期时间
     * @return string
     */
    public function createToken($userInfo, $lifetime = 0)
    {
        $option['expire'] = $lifetime;
        $token = self::encode($userInfo, 0);
        \cookie($this->storeName, $token, $option);
        return $token;
    }
}