<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/29
 */

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\SocialFriend as SocialFriendModel;

/**
 * 交友类
 * @package app\common\service
 */
class SocialFriend extends Service
{
    protected $model;

    public function __construct(SocialFriendModel $model)
    {
        $this->model = $model;//\model('SocialFriend');
    }

    /**
     * 同意交友
     * 写入两次,可能是一个表
     * @return boolean
     */
    function agree($user_id, $friend_id)
    {
        $data_1 = [
            'user_id' => $user_id,
            'friend_id' => $friend_id,
        ];
        $data_2 = [
            'user_id' => $friend_id,
            'friend_id' => $user_id,
        ];

        return $this->model->setPartition($data_1)->insert($data_1)
            && $this->model->setPartition($data_2)->insert($data_2);
    }

    /**
     * 是否是朋友
     * @return boolean
     */
    function isFriend($user_id, $friend_id)
    {
        $where = [
            'user_id' => $user_id,
            'friend_id' => $friend_id
        ];
        return $this->model->whereExists($where);
    }
}