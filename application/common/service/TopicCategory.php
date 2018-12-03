<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\TopicCategory as TopicCatetoryModel;

class TopicCategory extends Service
{
    protected $modelTopicCategory;

    public function __construct(TopicCatetoryModel $modelTopicCategory)
    {
        $this->modelTopicCategory = $modelTopicCategory;
    }

    /**
     * 列举可用的上级分类
     */
    public function listAvailableParent(int $id)
    {
        $exceptId = $this->getChildrenIds($id);
        $exceptId[] = $id;
        $where = [['id', 'not in', $exceptId]];
        return $this->modelTopicCategory
            ->field('id,title')
            ->where($where)
            ->select();
    }

    /**
     * 读取全部下级分类Id
     * @param int $id 分类id
     * @return array [id分类主键]
     */
    public function getChildrenIds(int $id)
    {
        $rt = [];

        $parentIds = [$id];// 读取 parent_id in $parentIds 的id到$rt;
        while ($parentIds) {
            $where = ['parent_id'=> $parentIds];
            $rs = $this->modelTopicCategory->field('id')->where($where)->select();

            $parentIds = [];
            if ($rs) {
                $parentIds = array_column($rs->toArray(), 'id' );
                $rt = array_merge($rt, $parentIds);
            }
        }
        return $rt;
    }


    /**
     * 是否是祖先id
     * @param int $id 当前id
     * @param int $ancestor_id 上级id
     * @return bool
     * - 保存一个分类要检查id是否是祖先上是parent_id祖先,否则互为祖先的死循环
     */
    private function isAncestorId(int $id, int $ancestor_id)
    {
        $rt = false;
        while ($parent_id = $this->modelTopicCategory->where(['id' => $id])->value('parent_id')) {
            if ($ancestor_id === $parent_id) {
                $rt = true;
                break;
            }
            $id = $parent_id;
        }
        return $rt;
    }

}