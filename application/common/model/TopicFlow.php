<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class TopicFlow extends BaseModel
{
    /**
     * 添加
     * @param array $data
     * @param bool $noParent 没有父记录:false 需要跟踪父ID
     * @return int|string
     */
    function add(array $data, $traceParent = true)
    {
        $data['parent_id'] = 0;
        if ($traceParent && $last = self::getLast($data['snap_id'])) {
            $data['parent_id'] = $last['id'];
            $data['user_id'] = $last['user_id'];
            $this->changeParentLast($data['parent_id']);
        }
        return $this->insert($data);
    }

    /**
     * 读取最后一条
     * @param int $snap_id
     * @return array
     */
    function getLast(int $snap_id)
    {
        return $this
            ->where(['snap_id' => $snap_id, 'is_last' => 1])
            ->order('id desc')
            ->find();
    }

    /**
     * 设置父记录is_last字段
     * @param int $id
     * @return static
     */
    private function changeParentLast(int $id)
    {
        return $this->where(['id' => $id])
            ->limit(1)
            ->update(['is_last' => 0]);
    }

    /**
     * 检查状态
     * @param int $snap_id
     * @param int $state
     * @return bool
     */
    function checkState(int $snap_id, int $state): bool
    {
        return $this->getState($snap_id) == $state;
    }

    /**
     * 获取状态
     */
    function getState(int $snap_id)
    {
        return $this
            ->where(['snap_id' => $snap_id, 'is_last' => 1])
            ->order('id desc')
            ->value('state');
    }
}