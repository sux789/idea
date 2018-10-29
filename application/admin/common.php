<?php
/**
 * 后台管理通用函数
 * User: xiang.su@qq.com
 * Date: 2018/10/29
 */

/**
 * 手机号转id,用于大数据量高效索引或分表
 * @param string $mobile 手机号码
 * @return int
 */
function get_mobile_id($mobile)
{
    return sprintf('%u', crc32($mobile));
}

