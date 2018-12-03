<?php

namespace app\index\controller;

use app\common\code_doc\ParseServer;
use app\common\adapter\IndexController;
use app\common\JsonResponse;

class Index extends IndexController
{
    const ERRNO_NOT_LOGIN = 100;

    public function index()
    {
        return redirect('/reli');

    }

    /**
     * 无意义实例例子分类级分类+子孙id
     * @param int $id 分类id
     * @return array [parents:可用上级分类,children_ids:子孙id]
     */
    public function getCategoryFamily($id = 1)
    {
        $data =
            [
                'parents' => call_service('TopicCategory/listAvailableParent', $id),
                'children_ids' => call_service('TopicCategory/getChildrenIds', $id),
            ];
        return $data;
    }

    /**
     * 无意义实例粉丝统计+到user_id为200社交6度关系
     * @param int $user_id 用户id
     * @return array [social_count关注统计,relation_path:关系路径]
     */
    public function getSocialCount(int $user_id = 1)
    {

        $argvRelation =
            [
                'aId' => $user_id,
                'bId' => 200,
            ];
        $data =
            [
                'social_count' => call_service('SocialFans/sum', $user_id),
                'relation_path' => call_service('SocialFriend/listSixDegreeRelation', $argvRelation),
            ];
        return $data;
    }

    /**
     * 无意义实例审核相关接口
     * @table topic_flow
     * @return array [[snap_id,note]]
     */
    public function audit()
    {

        $argv = ['snap_id' => 5, 'admin_id' => 6, 'note' => 'pass'];
        $rs = call_service('TopicAudit/withdraw', $argv);// 下架主题
        $rs = call_service('TopicAudit/approve', $argv);
        $rs = call_service('TopicAudit/disapprove', $argv);
        $rs = call_service('TopicAudit/delete', ['snap_id' => 5, 'user_id' => 1]);
        return call_service('TopicAudit/history', ['snap_id' => 5]);
    }

    /**
     * 无意义实例关注交友接口
     * @param int $target_user_id
     * @return bool
     * @throws \app\common\exception\CodeException
     */
    public function testSocial(int $target_user_id = 100)
    {
        // 验证身份放在中间件中，不是这儿
        if (!$this->user_id) {
            code_exception(self::ERRNO_NOT_LOGIN);
        }

        // 关注接口
        $argv = ['fans_id' => $this->user_id, 'idol_id' => $target_user_id];
        $rs_1 = call_service('SocialIdol/follow', $argv);

        // 交友
        $argv = ['user_id' => $this->user_id, 'friend_id' => $target_user_id];
        $rs_2 = call_service('SocialFriend/agree', $argv);

        return $rs_1 && $rs_2;
    }
}
