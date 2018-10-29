<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 调用服务
 * @param string $path 路径
 * @param array $argv 参数
 */
function call_service($path, $argv = [])
{
    return \app\common\service_client\ServiceClient::handle($path, $argv);
}

/**
 * 统一的错误处理,有变量渲染格式同模板
 * @param int $code 错误代码
 * @param array $argv 消息的变量 如['nickname'=>'Li','mobile'=>'136..']对应"hello {$nickname},check you {$mobile}"
 * @return \Exception
 */
function code_exception($code, $argv = [])
{
    $msg = config("exception_zh_cn.$code");

    if ($msg && $argv) {
        $map = [];
        foreach ($argv as $key => $value) {
            $map["{\$$key}"] = $value;
        }
        $msg = strtr($msg, $map);
    }
    throw new \app\common\exception\CodeException($msg, $code);
}


/**
 * 高效转换类名称为小写,比如用于类名转换表名称,反函数classname
 * 重构原逻辑: strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"))
 */
function lowercase_classname($str)
{
    $keys = [
        'A' => 'a',
        'B' => 'b',
        'C' => 'c',
        'D' => 'd',
        'E' => 'e',
        'F' => 'f',
        'G' => 'g',
        'H' => 'h',
        'I' => 'i',
        'J' => 'j',
        'K' => 'k',
        'L' => 'l',
        'M' => 'm',
        'N' => 'n',
        'O' => 'o',
        'P' => 'p',
        'Q' => 'q',
        'R' => 'r',
        'S' => 's',
        'T' => 't',
        'U' => 'u',
        'V' => 'v',
        'W' => 'w',
        'X' => 'x',
        'Y' => 'y',
        'Z' => 'z',
    ];
    $rt = '';
    for ($i = 0; $char = $str[$i] ?? ''; $i++) {
        $isUpper = isset($keys[$char]);//是否是大写
        if ($isUpper) {
            $rt .= $i ? '_' . $keys[$char] : $keys[$char];
        } else {
            $rt .= $char;
        }
    }
    return $rt;
}

/**
 * 转换小写字符串类为Psr类名.反函数 lowercase_classname
 */
function classname($str)
{
    return join('', array_map('ucfirst', explode('_', $str)));
}

/**
 * 统一存储密码加密方式
 * @param string $password 密码
 * @return string
 */
function encode_password($password)
{
    return md5("{$password}{$password[3]}{$password[0]}{$password[1]}");
}