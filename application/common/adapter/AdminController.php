<?php

namespace app\common\adapter;

use think\App;
use think\Controller;

class AdminController extends Controller
{
    protected $middleware = [
        '\app\common\middleware\AdminAuth::class' => [
            'except' => ['login']
        ],
    ];
    protected $operator_id=0;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->assign('static_uri','/sb-admin-2');
        if($admin_auth=session('admin_auth')){
            $this->operator_id=$admin_auth['operator_id'];
            $this->assign($admin_auth);
        }
        $this->view->engine->layout('layout');
    }
}
