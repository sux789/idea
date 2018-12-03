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

        return $this->modelSocialFriend->save($data_1)
            && $this->modelSocialFriend->save($data_2);
    }


    /**
     * 社交6度关系算法简化版
     * @param int $aId 用户a的user_id
     * @param int $bId 用户b的user_id
     * @return array
     * - 可能结果会非常非常大，所以应该有条数限制
     * - 当前取双方二度好友实际得到三度关系，才4次比较；如果取双方三度好友得到5度关系，但是会有9次比较
     */
    public function listSixDegreeRelation(int $aId, int $bId)
    {
        $rt = [];
        $endPoins = [$aId => true, $bId => true];
        $a1 = $this->modelSocialFriend->getFirendMapping($aId);// a一度好友
        $b1 = $this->modelSocialFriend->getFirendMapping($bId); // b 一度好友

        // step 1 :a->a1->b
        $idA1B1 = array_intersect_key($a1, $b1);
        $rt = array_keys($idA1B1);
        // step 2:a->b2->b1->b
        $b2 = $this->modelSocialFriend->getFirendMapping(array_keys($b1));// b的二度好友
        $b2 = array_diff_key($b2, $b1, $endPoins);
        $idA1B2B1 = array_intersect_key($a1, $b2);
        foreach ($idA1B2B1 as $b2Id => $b1Id) {
            $rt[] = [$b2Id, $b1Id];
        }
        // step 3: a->a1->a2->b
        $a2 = $this->modelSocialFriend->getFirendMapping(array_keys($a1));//  a的二度好友
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