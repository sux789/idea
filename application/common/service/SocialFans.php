<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\SocialFans as SocialFansModel;

/**
 * 类 关注的粉丝表,用于通知
 * @package app\common\service
 */
class SocialFans extends Service
{
    private $modelFans;

    public function __construct(SocialFansModel $modelFans)
    {
        $this->modelFans = $modelFans;
    }

    /**
     * 读取粉丝数据
     * @param int $last_id 上一页最小ID
     * @param int $count 每页条数
     * @return array
     */
    function listFans($idol_id, $last_id = 0, $count = 10)
    {
        $where = [['idol_id' ,'=', $idol_id]];
        if ($last_id) {
            $where[] = ['id', '<', $last_id];
        }

        return $this->modelFans
                ->setPartition(['idol_id' => $idol_id])
                ->field('id,fans_id,idol_id')
                ->where($where)
                ->limit($count)
                ->select() ?? [];
    }

}