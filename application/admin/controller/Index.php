<?php

namespace app\admin\controller;
use app\common\adapter\AdminController;
class Index extends AdminController
{
    public function index()
    {
        print_r($this->admin_id);

        return view();
    }
}
