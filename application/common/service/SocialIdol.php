<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\SocialIdol as SocialIdolModel;
use app\common\model\SocialFans as SocialFansModel;


/**
 * 关注的偶像
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

    /**
     * 关注偶像
     * @return bool
     * 同时写两份个表
     */
    function follow(int $fans_id, int $idol_id)
    {
        $data = ['fans_id' => $fans_id, 'idol_id' => $idol_id];
        return $this->modelSocialIdol->save($data)
            && $this->modelSocialFans->save($data);
    }
}