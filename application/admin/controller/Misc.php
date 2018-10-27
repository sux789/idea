<?php

namespace app\admin\controller;

/**
 * Class 杂项类
 * 展示一些说明及无需权限的功能
 */

use app\common\system_service\auth\Auth;

class Misc
{

    const ERRNO_MOBILE = 10100101;
    const ERRNO_PASSWORD = 10100102;


    public function login()
    {
        app('auth')->delete();
        return view('login');
    }

    public function verifyLogin($mobile = '', $password = '')
    {
        // 假设用户数据
        $administrators = [
            '15001173500' => [1, 'c828743f8acab7623a1b03c4f6e295bb', 'sux'],
        ];

        $admin = $administrators[$mobile] ?? [];

        if (!$admin) {
            code_exception(self::ERRNO_MOBILE);
        }

        list($admin_id, $saved_password, $admin_name) = $admin;

        if (md5($password) != $saved_password) {
            code_exception(self::ERRNO_PASSWORD);
        }

        $authInfo = ['user_id' => $admin_id, 'user_name' => $admin_name];
        app('auth')->init(Auth::TYPE_ADMIN);
        app('auth')->createToken($authInfo);
        return $authInfo;
    }
}
