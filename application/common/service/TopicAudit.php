<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/31
 */

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\TopicFlow;
use app\common\model\TopicPub;
use app\common\model\TopicSnap;

/**
 * 主题审核服务
 * 背景:类似抖音,一个视频对应一个主题
 * @index snap_id+is_last 读取当前状态,及历史
 * @index state + is_last 读取待审核
 * @package app\common\service
 */
class TopicAudit extends Service
{
    const ST_UNAPPROVE = 10;//状态:打回草稿
    const ST_APPLY = 20;//状态:待审核
    const ST_PUB = 30;//状态:发布
    const ST_WITHDRAW = 40;//状态:下架
    const ST_DEL = 50; //状态:删除

    const ERRNO_APPLY_EXISTS = 20100111;//错误码:已经申请过
    const ERRNO_APPROVE_UNAPPLIED = 20100121;//错误码:未申请
    const ERRNO_WITHDRAW_NOT_EXISTS = 20100131;//错误码:主题不存在上架列表中
    const ERRNO_DELETE_WRONG_OWNER = 20100141;//错误码:只能删除user_id自己的快照
    const ERRNO_DELETE_EXISTS = 20100142;//错误码:已经删除

    protected $modelTopicSnap;// 快照
    protected $modelTopicFlow;// 流程
    protected $modelTopicPub;// 发布

    function __construct(TopicSnap $modelTopicSnap, TopicFlow $modelTopicFlow, TopicPub $modelTopicPub)
    {
        $this->modelTopicSnap = $modelTopicSnap;
        $this->modelTopicFlow = $modelTopicFlow;
        $this->modelTopicPub = $modelTopicPub;
    }

    /**
     * 下架撤稿
     */
    function withdraw($snap_id, $admin_id, $note = '')
    {
        $exists = $this->modelTopicPub->existSnapId($snap_id);
        if (!$exists) {
            code_exception(self::ERRNO_WITHDRAW_NOT_EXISTS);
        }

        $data = $this->getArgv();
        $data['state'] = self::ST_WITHDRAW;
        $del = $this->modelTopicPub->deleteBySnapId($snap_id); // 成功删除已经存在
        return $del && $this->modelTopicFlow->add($data);
    }

    /**
     * 通过上架
     */
    function approve($snap_id, $admin_id, $note = '')
    {
        $checkState = $this->modelTopicFlow->checkState($snap_id, self::ST_APPLY);
        if (!$checkState) {
            code_exception(self::ERRNO_APPROVE_UNAPPLIED, ['snap_id' => $snap_id]);
        }

        $state_row_id = 0;
        $user_id = $this->modelTopicSnap->getUserId($snap_id);
        if ($user_id) {
            $data = ['snap_id' => $snap_id, 'user_id' => $user_id];
            $state_row_id = $this->modelTopicPub->insertGetId($data);
        }

        $rt = false;
        if ($state_row_id) {
            $data = $this->getArgv();
            $data['state'] = self::ST_PUB;
            $data['user_id'] = $user_id;
            $data['state_row_id'] = $state_row_id;
            $rt = $this->modelTopicFlow->add($data);
        }
        return $rt;
    }

    /**
     * 打回草稿
     */
    function disapprove($snap_id, $admin_id, $note = '')
    {
        if (!$this->modelTopicFlow->checkState($snap_id, self::ST_APPLY)) {
            code_exception(self::ERRNO_APPLY_MISS_SATAE, ['snap_id' => $snap_id]);
        }
        $data = $this->getArgv();
        $data['state'] = self::ST_UNAPPROVE;
        return $this->modelTopicFlow->add($data);
    }

    /**
     * 申请审核
     */
    function apply($user_id, $upload_id, $url, $description = '')
    {
        if ($this->modelTopicSnap->isApplied($upload_id)) {
            code_exception(self::ERRNO_APPLY_EXISTS);
        }

        $data = $this->getArgv();
        $snap_id = $this->modelTopicSnap->insertGetId($data);
        $flowData = [
            'snap_id' => $snap_id,
            'user_id' => $user_id,
            'state' => self::ST_APPLY,
        ];
        return $this->modelTopicFlow->add($flowData, false);
    }

    /**
     * 删除快照
     */
    function delete(int $snap_id, int $user_id)
    {
        $snap = $this->modelTopicSnap->find($snap_id);
        $owner_id = $snap['user_id'] ?? 0;

        if ($user_id !== $owner_id) {
            code_exception(self::ERRNO_DELETE_WRONG_OWNER);
        }

        $deleted = $this->modelTopicFlow->checkState($snap_id, self::ST_DEL);
        if ($deleted) {
            code_exception(self::ERRNO_DELETE_EXISTS);
        }

        $this->modelTopicPub->deleteBySnapId($snap_id);//如果已经发布则删除

        $data = $this->getArgv();
        $data['state'] = self::ST_DEL;
        return $this->modelTopicFlow->add($data);
    }

    /**
     * 读取审核流程
     */
    function history(int $snap_id, string $order = 'DESC')
    {
        $data = $this->getArgv();
        echo "<h1>$order</h1>";
        print_r($data);
        return $this->modelTopicFlow->history($snap_id, $order);
    }
}