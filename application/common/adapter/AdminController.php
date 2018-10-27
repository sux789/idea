<?php

namespace app\common\adapter;

use think\App;
use think\Controller;

class AdminController extends Controller
{
    protected $middleware = [
        '\app\common\middleware\AdminAuth::class' => [],
    ];
    protected $operator_id=0;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if($auth=app('auth')->get()){
            $this->admin_id=$auth['user_id'];
            $this->assign(['admin_name'=>$auth['user_name']]);
        }
        $this->view->engine->layout('layout');
    }
}
