<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/26
 */

namespace app\common\system_service\auth;


class AuthAdmin extends AuthPc
{
    public function __construct($storeName,$type)
    {
        // PC前后端同名不影响安全,但是防止使用默认名称一致
        parent::__construct('admin_' . $storeName,$type);
    }
}