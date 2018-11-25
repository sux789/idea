<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\TopicCategory as TopicCatetoryModel;

class TopicCategory extends Service
{
    protected $modelPostCategory;

    public function __construct(TopicCatetoryModel $modelPostCategory)
    {
        $this->modelPostCategory = $modelPostCategory;
    }

    /**
     * 添加一条数据
     * @param int $parent_id 付id
     * @table home_category,xxxzz,ssa; fsaf fsa2
     * @param $id $b $c
     * @param $data 提交数据
     * @return array []
     */
    function add($parent_id = 0, $title, $description = '', $sort = 0)
    {
        $argv = $this->getArgv();
        $this->modelPostCategory->insert($argv);
    }

    /**
     * 列举可用的上级分类
     * @param int $id
     * @return mixed
     */
    public function listAvailableParent(int $id)
    {
        $exceptId = $this->getChildrenIds($id);
        $exceptId[] = $id;
        $where = [['id', 'not in', $exceptId]];
        return $this->modelPostCategory
            ->field('id,title')
            ->where($where)
            ->select();
    }

    /**
     * 读取全部下级分类Id
     * @param int $id
     * @return array [id]
     */
    private function getChildrenIds(int $id)
    {
        $rt = [];

        $parentIds = [$id];// 读取 parent_id in $parentIds 的id到$rt;
        while ($parentIds) {
            $where = ['parent_id'=> $parentIds];
            $rs = $this->modelPostCategory->field('id')->where($where)->select();

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
     * 目的1:保存一个分类要检查id是否是祖先上是parent_id祖先,否则互为祖先的死循环
     */
    public function isAncestorId($id, int $ancestor_id)
    {
        $rt = false;
        while ($parent_id = $this->modelPostCategory->where(['id' => $id])->value('parent_id')) {
            if ($ancestor_id === $parent_id) {
                $rt = true;
                break;
            }
            $id = $parent_id;
        }
        return $rt;
    }

}