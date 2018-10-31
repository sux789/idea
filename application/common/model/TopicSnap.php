<?php

namespace app\common\model;

use app\common\adapter\BaseModel;

class TopicSnap extends BaseModel
{
    public function getUserId($snap_id){
        return $this->where(['id' => $snap_id])->value('user_id');
    }
}