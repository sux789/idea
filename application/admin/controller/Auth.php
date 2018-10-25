<?php

namespace app\admin\controller;

use app\common\adapter\AdminController;

class Auth extends AdminController
{
    public function login($mobile = '', $password = '')
    {

        $operators = [
            [1, '13683177948', 'c828743f8acab7623a1b03c4f6e295bb', 'liuk'],
            [2, '15001173500', 'c828743f8acab7623a1b03c4f6e295bb', 'sux'],
        ];

        $authState = 0;//登录状态
        $authInfo = [];
        if ($mobile && $password) {
            foreach ($operators as $item) {
                if ($item[1] === $mobile) {
                    $authState = 1;//手机号正确
                    if ($item[2] === md5($password)) {
                        $authState = 3;
                        $authInfo = ['operator_id' => $item[0], 'operator_name' => $item[3]];
                    }
                } else {
                    $authState = 2;
                }
            }
        }
        if ($authInfo) {
            session('admin_auth', $authInfo);
            return redirect('index/index');
        }
        $this->view->engine->layout(false);
        return $this->fetch('login');
    }

    public function logout()
    {
        session('admin_auth', null);
        return redirect('login');
    }
}
