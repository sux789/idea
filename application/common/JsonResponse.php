<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/17
 */

namespace app\common;

use think\response\Json;

/**
 * 自动对前端错误和返回进行统一的封装
 * - 自动对格式进行封装,就是不用手动去修改action的返回，比如 return 1;
 * - 配置'default_ajax_return'=>'app\\common\\JsonResponse';
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