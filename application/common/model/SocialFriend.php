<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class SocialFriend extends BaseModel
{
    /**
     * 读取读取好友id
     * @param mixed $userIds 用户ID
     * @return array [firend_id=>user_id]
     */
    public function getFirendMapping($userIds)
    {
        $rt = [];
        if ($userIds) {
            $rs = $this
                ->field('user_id,friend_id')
                ->where(['user_id' => $userIds])
                ->select()
                ;
            if ($rs) {
                $rt = array_column($rs->toArray(), 'user_id', 'friend_id');
            }
        }
        return $rt;
    }
}