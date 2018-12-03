<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class SocialIdol extends BaseModel
{

    /**
     * 读取偶像数据
     * @param int $last_id 上一页最小ID
     * @param int $count 每页条数
     * @return array
     */
    function listIdol($fans_id, $last_id = 0, $count = 10)
    {
        $where = [['fans_id', '=', $fans_id]];
        if ($last_id) {
            $where[] = ['id', '<', $last_id];
        }

        return $this
                ->setPartition(['fans_id' => $fans_id])
                ->field('id,fans_id,idol_id')
                ->where($where)
                ->limit($count)
                ->select();
    }
}