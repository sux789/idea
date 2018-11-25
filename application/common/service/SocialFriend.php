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
    protected $modelSocialFriend;

    public function __construct(SocialFriendModel $model)
    {
        $this->modelSocialFriend = $model;//
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

        return $this->modelSocialFriend->setPartition($data_1)->insert($data_1)
            && $this->modelSocialFriend->setPartition($data_2)->insert($data_2);
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
        return $this->modelSocialFriend->whereExists($where);
    }

    /**
     * 读取读取好友id
     * @param mixed $userIds 用户ID
     * @return array [firend_id=>user_id]
     */
    private function getFirendId($userIds)
    {
        $rt = [];
        if ($userIds) {
            $rs = $this->modelSocialFriend
                ->field('user_id,friend_id')
                ->where(['user_id' => $userIds])
                ->select()
                ->toArray();
            if ($rs) {
                $rt = array_column($rs, 'user_id', 'friend_id');
            }
        }
        return $rt;
    }

    /**
     * 社交度关系算法简化版
     * @param int $aId 用户a的user_id
     * @param int $bId 用户b的user_id
     * @return array
     * - 可能结果会非常非常大，所以应该有条数限制
     * - 扩充多少度都这个思路，需要仔细判断
     */
    public function listSixDegreeRelation(int $aId, int $bId)
    {
        $rt = [];
        $endPoins = [$aId => true, $bId => true];
        $a1 = $this->getFirendId($aId);// a一度好友
        $b1 = $this->getFirendId($bId); // b 一度好友

        // step 1 :a->a1->b
        $idA1B1 = array_intersect_key($a1, $b1);
        $rt = array_keys($idA1B1);
        // step 2:a->b2->b1->b
        $b2 = $this->getFirendId(array_keys($b1));// b的二度好友
        $b2 = array_diff_key($b2, $b1, $endPoins);
        $idA1B2B1 = array_intersect_key($a1, $b2);
        foreach ($idA1B2B1 as $b2Id => $b1Id) {
            $rt[] = [$b2Id, $b1Id];
        }
        // step 3: a->a1->a2->b
        $a2 = $this->getFirendId(array_keys($a1));//  a的二度好友
        $a2 = array_diff_key($a2, $a1, $endPoins);
        $idA1A2B1 = array_intersect_key($a2, $b1);
        foreach ($idA1A2B1 as $a2Id => $a1Id) {
            $rt[] = [$a1Id, $a2Id];
        }
        // step 4: a->a1->a2->b1->b
        $idA1A2B2B1 = array_intersect_key($a2, $b2);
        foreach ($idA1A2B2B1 as $a2Id => $a1Id) {
            $rt[] = [$a1Id, $a2Id, $b2[$a2Id]];
        }
        return $rt;
    }
}