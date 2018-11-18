<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/17
 */

function fake_data($type, $fileds, $isMulti = false)
{
    $type = strtolower($type);

    $value = null;
    if ('int' == substr($type, 0, 3)) {
        $value = 1;
    } elseif ('bool' == substr($type, 0, 4)) {
        $value = true;
    } elseif ('array' == $type) {
        $value = [];
    }

    // 伪造数据
    if ([] === $value && $fileds) {
        foreach ($fileds as $name => $info) {
            $value[$name] = $info['title'] ?? '';
        }
    }

    return $isMulti ? [$value] : $value;
}

function fake_json($type, $fileds = [], $isMulti = false)
{
    $value = fake_data($type, $fileds, $isMulti);
    $rt = '';
    if (null !== $value) {
        // 二维格式
        $wapper = ['total' => 29,
            'count' => 9,
            'list_rows' => 10,
            'rows' => $value,
        ];

        $data = $isMulti ? $wapper : $value;
        $data = \app\common\JsonResponse::format($data);
        $rt = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    return $rt;
}

/**
 * 用于显示返回结果
 * @param $type
 * @param array $fileds
 * @param bool $isMulti
 * @return mixed
 */
function fake_var_string($type, $fileds = [], $isMulti = false)
{
    $rt = '';
    if ($fileds) {
        $value = fake_data($type, $fileds, $isMulti);
        $rt = var_export($value, true);
    } elseif ($type) {
        $rt = $type;
    }
    return $rt;
}

/**
 * 根据规范判断是否是请求方法
 * @param string $actionName
 * @return string
 */
function get_http_method($actionName)
{
    $isGet = 'get' == substr($actionName, 0, 3) or 'list' == substr($actionName, 0, 4);
    return $isGet ? 'GET' : 'POST';
}

