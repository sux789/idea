<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class TopicSnap extends BaseModel
{
    /**
     * 读取user_id
     * @param int $snap_id
     * @return int
     */
    public function getUserId(int $snap_id): int
    {
        return (int)$this->where(['id' => $snap_id])->value('user_id');
    }

    /**
     * 是否申请过
     * @param int $upload_id
     * @return bool
     */
    function isApplied(int $upload_id): bool
    {
        return (bool)$this
            ->where(['upload_id' => $upload_id])
            ->value('upload_id');
    }
}