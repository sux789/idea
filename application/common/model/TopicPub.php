<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class TopicPub extends BaseModel
{
    /**
     * 删除
     * @param int $snap_id
     * @return bool
     * @throws \Exception
     */
    function deleteBySnapId(int $snap_id): bool
    {
        $where = ['snap_id' => $snap_id];
        return $this
            ->where($where)
            ->limit(1)
            ->delete();
    }

    /**
     * 是否存在
     * @param int $snap_id
     * @return bool
     */
    function existSnapId(int $snap_id): bool
    {
        $where = ['snap_id' => $snap_id];
        return (bool)$this->where($where)->value('snap_id');
    }

}