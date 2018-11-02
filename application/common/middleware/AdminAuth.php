<?php

namespace app\common\middleware;

use app\common\system_service\auth\AuthProxy;

class AdminAuth
{
    public function __construct()
    {
        // 验证类型初始化,否则会更具配置自动初始化
        app('auth')->init(AuthProxy::TYPE_ADMIN);
    }

    public function handle($request, \Closure $next)
    {
        if (!$this->isLogin()) {
            return redirect('misc/login');
        }
        return $next($request);
    }

    private function isLogin()
    {
        return app('auth')->get();
    }
}