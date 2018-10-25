<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class SocialFriend extends BaseModel
{
    /**
     * 设置分表
     */
    protected function initialize()
    {
        parent::initialize();
        $this->partitionStep = 100 * 1000;// 设置分表数量
        $this->partitionField = 'user_id';// 设置分表字段
    }
}