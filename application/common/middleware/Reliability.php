<?php

namespace app\common\middleware;

use think\Db;
/**
 * 可靠性中间件,帮助监控和调试
 * @package app\common\middleware
 */
class Reliability
{
    public function handle($request, \Closure $next)
    {
        Db::listen(function ($sql, $time, $explain) {
            // 记录SQL
            echo $sql . ' [' . $time . 's]';
            // 查看性能分析结果
            dump($explain);
        });
        return $next($request);
    }

    private function isOn(){
        return !empty(cookie('reliability'));
    }
}