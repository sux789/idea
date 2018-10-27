<?php

namespace app\common\adapter;

use think\App;
use think\Controller;

class RelyController extends Controller
{
    protected $middleware = [
        /*'\app\common\middleware\AdminAuth::class' => [
            'except' => ['login']
        ],*/
    ];
    protected $operator_id=0;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->view->engine->layout('layout');
    }
}
