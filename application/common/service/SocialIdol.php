<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\SocialFans as SocialFansModel;
use app\common\model\SocialIdol as SocialIdolModel;

/**
 * 类 关注的偶像
 * @package app\common\service
 */
class SocialIdol extends Service
{
    protected $modelIdol;
    protected $modelFans;

    public function __construct(SocialFansModel $modelFans, SocialIdolModel $modelIdol)
    {
        $this->modelFans = $modelFans;
        $this->modelIdol = $modelIdol;
    }

    /**
     * 关注偶像
     * 同时写两份表
     * @param integer $fans_id 偶像id
     * @param integer $idol_id 粉丝id
     * @return integer
     */
    function follow($fans_id, $idol_id)
    {
        $data = ['fans_id' => $fans_id, 'idol_id' => $idol_id];
        return $this->modelIdol->save($data)
            && $this->modelFans->save($data);
    }

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

        return $this->modelIdol
                ->setPartition(['fans_id' => $fans_id])
                ->field('id,fans_id,idol_id')
                ->where($where)
                ->limit($count)
                ->select() ?? [];
    }
}