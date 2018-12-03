<?php

namespace app\common\adapter;

use think\App;
use think\Controller;
use think\Request;
class ReliController extends Controller
{

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if(!request()->isAjax())
        $this->view->engine->layout('layout');
    }
}
