<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\SocialFans;
use app\common\model\SocialIdol;
use app\common\model\SocialFriend;

class Social extends Service
{
    private $modelIdel;
    private $modelFans;

    public function __construct(SocialFans $modelFans, SocialIdol $modelIdel)
    {
        $this->modelFans = $modelFans;
        $this->modelIdel = $modelIdel;
    }

    /**
     * 关注
     * 同时写两份表
     * @param integer $fans_id 偶像id
     * @param integer $idel_id 粉丝id
     * @return integer
     */
    function follow( $fans_id,$idol_id){
        $data=['fans_id'=>$fans_id,'idol_id'=>$idol_id];
        return $this->modelIdel->save($data)
            && $this->modelFans->save($data);
    }

    /**
     * 读取粉丝数据
     * @param $idel_id
     */
    function listFans($idel_id){

    }

    /**
     * 读取偶像数据
     * @param $fans_id
     */
    function listIdol($fans_id){

    }

    /**
     * 同意交友
     * 写入两次,可能是一个表
     */
    function makeFriend($user_id,$friend_id){

    }

    /**
     * 读取朋友列表
     */
    function listFriend($user_id){

    }
}