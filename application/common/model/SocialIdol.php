<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class SocialIdol extends BaseModel
{
    /**
     * 构建时调用
     */
    protected function initialize()
    {
        parent::initialize();
        $this->partitionStep = 100 * 1000;// 设置分表数量
        $this->partitionField = 'fans_id';// 设置分表字段
    }
}