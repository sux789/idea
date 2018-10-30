<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\PostCategory as ModelPostCatetory;

class PostCategory extends Service
{
    protected $modelPostCategory;

    public function __construct(ModelPostCatetory $model)
    {
        $this->modelPostCategory = $model;
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
     * 读取一条数据
     */
    function get($id = 0)
    {
        return $this->modelPostCategory->find($id);
    }

    /**
     * 读取多条数据,可以根据
     */
    function listAll()
    {
        return $this->modelPostCategory->select();
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