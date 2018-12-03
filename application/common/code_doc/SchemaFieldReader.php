<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/17
 */

namespace app\common\code_doc;

/**
 * 自动读取数据库字段结构用于补充文档
 * @package app\common\code_doc
 */
class SchemaFieldReader
{

    /**
     * 验证表名称是否可用
     * @param mixed $tableName
     * @return array
     */
    public static function vaildTableName($tableName)
    {
        $schema = self::listSchema();
        $rt = [];
        foreach ((array)$tableName as $table) {
            if (isset($schema[$table])) {
                $rt[] = $table;
            }
        }
        return $rt;
    }

    private static function listSchema()
    {
        static $rt = [];
        if (!$rt) {
            $rt = ParseServer::listSchema();
        }
        return $rt;
    }

    /**
     * 从类名称解析表
     * @param string $class
     * @return array []
     */
    public static function getTableByClass($class = '')
    {
        $tabeName = preg_replace('/[A-Z]/', '_\\0', $class);
        $tabeName = strtolower(trim($tabeName, '_'));
        return self::vaildTableName($tabeName);
    }

    /**
     * 从数据库读取参数注释
     * @param $tables
     * @return array
     */
    public static function listFieldBySchema($tables)
    {
        static $rts;
        $key = join('|', $tables);
        if (!isset($rts[$key])) {
            $schema = self::listSchema();
            $data = [];
            foreach ($tables as $table) {
                foreach ($schema[$table]['detail'] as $field) {
                    $type = explode('(', $field['Type'])[0];
                    $type = false === strpos($type, 'int') ? 'string' : 'int';
                    $data[$field['Field']] = ['type' => $type, 'title' => $field['Comment']];
                }
            }
            $rts[$key] = $data;
        }
        return $rts[$key];
    }

}