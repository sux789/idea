<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/24
 */

namespace app\api_v1\controller;

use app\common\adapter\ApiController;

class Social extends ApiController
{
    function index(){
        return call_service('Social/follow',['fans_id'=>1,'idol_id'=>100*1000+1]);
    }
}