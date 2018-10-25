<?php

namespace app\common\adapter;

use think\App;
use think\Controller;

class IndexController extends Controller
{
    protected $middleware = [
        '\app\common\middleware\Reliability::class' => [
        ],
    ];

    protected $user_id=0;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }
}
