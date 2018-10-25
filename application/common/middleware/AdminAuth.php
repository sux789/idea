<?php

namespace app\common\middleware;


class AdminAuth
{
    public function handle($request, \Closure $next)
    {
        if(!$this->isLogin()){
            return redirect('auth/login');
        }
        return $next($request);
    }

    private function isLogin(){
        return !empty(session('admin_auth'));
    }
}