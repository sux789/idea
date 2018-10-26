<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/22
 */

namespace app\common\adapter;

use think\model;

class BaseModel extends model
{
    protected $partitionStep = 0;//设置分表数量
    protected $partitionField = '';//设置分表字段

    /**
     * 在model层里面分表算法
     * @param $data
     * @return bool
     */
    function setPartitionName($data)
    {
        $step = $this->partitionStep ?? 0;
        $field = $this->partitionField ?? '';
        $value = $data[$field] ?? 0;
        $seq = 0;
        if ($value && $step) {
            $seq = ceil($value / $step);
        }

        $this->table = lowercase_classname($this->getName());
        if ($seq) {
            $this->table .= '_' . $seq;
        }
        return true;
    }

    /**
     * 读写都设置表名称
     */
    protected function initialize()
    {
        parent::initialize();
        self::event('before_write', [$this, 'setPartitionName']);
        self::event('before_select', [$this, 'setPartitionName']);
        self::event('before_find', [$this, 'setPartitionName']);
    }

}