<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/17
 */

namespace app\common;

use think\response\Json;

/**
 * Class 对前端统一的JSON输出类
 * @example config('default_ajax_return','app\\common\\JsonResponse');
 * @package app\common
 */
class JsonResponse extends Json
{
    /**
     * @inheritdoc
     */
    public function data($data)
    {
        $this->data = self::format($data);
        return $this;
    }

    /**
     * 统一json格式化
     * @param mixed $data
     * @return array
     */
    public static function format($data = null, $error = '', $errno = 0)
    {
        $rt = [
            'error' => $error,
            'errno' => $errno,
        ];
        if (null !== $data) {
            $rt['data'] = $data;
        }
        return $rt;
    }
}