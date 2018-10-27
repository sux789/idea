<?php

namespace app\common\adapter;

use think\App;
use think\Controller;

class ReliController extends Controller
{

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->view->engine->layout('layout');
    }
}
