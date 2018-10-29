<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/22
 */

namespace app\common\adapter;

use think\model;

class BaseModel extends model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    /**
     * 在model层里面分表算法
     * @param $data
     * @return bool
     */
    function setPartition($data)
    {
        $this->table = lowercase_classname($this->getName());
        $config = self::getPartitionConfig($this->table);
        dump($config);
        $step = $config['step'] ?? 0;
        $field = $config['field'] ?? '';
        $value = $data[$field] ?? 0;
        $seq = 0;
        if ($value && $step) {
            $seq = ceil($value / $step);
        }
        if ($seq) {
            $this->table .= '_' . $seq;
        }
        return $this;
    }

    /**
     * 读表分区配置
     * @param string $table 表名称
     * @return mixed
     */
    private static function getPartitionConfig($table)
    {
        static $rts = [];
        if (!isset($rts[$table])) {
            $rts[$table] = config("partition.{$table}");
        }
        return $rts[$table];
    }

    /**
     * 读写时设置分表名称
     */
    protected function initialize()
    {
        parent::initialize();
        self::event('before_write', [$this, 'setPartition']);
    }

}