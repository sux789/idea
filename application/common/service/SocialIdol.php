<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\SocialIdol as SocialIdolModel;
use app\common\model\SocialFans as SocialFansModel;


/**
 * 类 关注的偶像
 * @package app\common\service
 */
class SocialIdol extends Service
{
    protected $modelSocialIdol;
    protected $modelSocialFans;

    public function __construct(SocialFansModel $modelSocialFans, SocialIdolModel $modelSocialIdol)
    {
        $this->modelSocialFans = $modelSocialFans;
        $this->modelSocialIdol = $modelSocialIdol;
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
        return $this->modelSocialIdol->save($data)
            && $this->modelSocialFans->save($data);
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

        return $this->modelSocialIdol
                ->setPartition(['fans_id' => $fans_id])
                ->field('id,fans_id,idol_id')
                ->where($where)
                ->limit($count)
                ->select() ?? [];
    }
}