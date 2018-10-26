<?php

namespace app\common\code_doc;

use think\Db;

/**
 * 类 提供数据库结构
 * @package app\common\code_doc
 * @todo 应根据配置处理多数据库情况
 */
class DatabaseSchema
{
    public static function handle()
    {
        $rs = Db::query('SHOW TABLE STATUS');
        $rt = array_column($rs, null, 'Name');
        foreach ($rt as $table => $cells) {
            $sql = "SHOW FULL COLUMNS FROM `$cells[Name]`";
            $cols = Db::query($sql);
            $rt[$table]['detail'] = array_column($cols, null, 'Field');
        }
        return $rt;
    }
}