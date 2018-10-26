<?php

namespace app\common\service;

use app\common\model\PostCategory as PostCatetoryModel;
use app\common\adapter\Service;

class PostCategory extends Service
{
    public function __construct(PostCatetoryModel $model)
    {
        $this->model = $model;
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
    }

    /**
     * 更新数据
     * @param $id
     * @param $data 提交数据
     */
    function update($id = 0, $data = [])
    {

    }

    /**
     * 读取一条数据
     */
    function get($id = 0, $title = '')
    {
        $where = [];
        if ($id) {
            $where = ['id' => $id];
        } elseif ($this) {
            $where = ['title' => ['like', "$title%"]];
        }

        $rt = [];
        if ($where) {
            $rt = $this->model->where($where)->find();
        }
        return $rt;
    }

    /**
     * 读取多条数据,可以根据
     */
    function lists($parent_id = false, $title = '')
    {

    }

    /**
     * 是否是祖先id
     * 目的1:保存一个分类要检查id是否是祖先上是parent_id祖先,否则互为祖先的死循环
     */
    public function isAncestorId($id, int $ancestor_id)
    {
        $rt = false;
        while ($parent_id = $this->model->where(['id' => $id])->value('parent_id')) {
            if ($ancestor_id === $parent_id) {
                $rt = true;
                break;
            }
            $id = $parent_id;
        }
        return $rt;
    }


}