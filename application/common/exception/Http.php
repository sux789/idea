<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/26
 */

namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\Response;
use app\common\JsonResponse;

/**
 * 类 统一错误处理及统一前端格式
 * @package app\common\exception
 */
class Http extends Handle
{
    public function render(Exception $e)
    {
        if ($e instanceof CodeException) {
            $data = JsonResponse::format(null, $e->getMessage(), $e->getCode());
            return Response::create($data, 'json');
        }
        return parent::render($e);
    }
}