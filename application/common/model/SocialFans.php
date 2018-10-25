<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class SocialFans extends BaseModel
{
    /**
     * 构建时调用
     */
    protected function initialize()
    {
        parent::initialize();
        $this->partitionStep = 100 * 1000;// 设置分表数量
        $this->partitionField = 'idol_id';// 设置分表字段
    }
}