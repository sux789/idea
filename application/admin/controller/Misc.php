<?php

namespace app\admin\controller;

/**
 * Class 杂项类
 * 展示一些说明及无需权限的功能
 */

use app\common\system_service\auth\AuthProxy;

class Misc
{
    const ERRNO_MOBILE_NOT_EXISTS = 10100101;
    const ERRNO_PASSWORD_WRONG = 10100102;

    /**
     * login form
     * @return \think\response\View
     */
    public function login()
    {
        // logout
        app('auth')->delete();
        return view('login');
    }

    /**
     * 登录校验
     * @param string $mobile 手机号
     * @param string $password 密码
     * @return array
     * @throws \app\common\exception\CodeException
     */
    public function verifyLogin($mobile = '', $password = '')
    {
        $user = call_service('admin/admin_user/find', $mobile);

        if (!$user) {
            code_exception(self::ERRNO_MOBILE_NOT_EXISTS, ['mobile' => $mobile]);
        }

        if (encode_password($password) != $user['password']) {
            code_exception(self::ERRNO_PASSWORD_WRONG);
        }

        $authInfo = ['user_id' => $user['id'], 'user_name' => $user['name']];
        app('auth')->init(AuthProxy::TYPE_ADMIN)->createToken($authInfo);

        return $authInfo;
    }

    /**
     * 演示批量初始化后台用户
     */
    public function initUser()
    {
        $data = [
            ['mobile' => '15001173500', 'password' => 'sux789', 'name' => 'sux'],
        ];

        foreach ($data as $item) {
            $item['mobile_id'] = mobile_to_id($item['mobile']);
            $exist = call_service('admin/admin_user/exists', $item['mobile_id']);

            if (!$exist) {
                $item['password'] = encode_password($item['password']);
                $id = call_service('admin/admin_user/add', $item);
                echo "<p>{$item['mobile']} has created </p>";
            } else {
                echo "<p>{$item['mobile']} exists!</p>";
            }
        }
    }
}
