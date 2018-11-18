<?php

namespace app\index\controller;

use app\common\code_doc\ParseServer;
use app\common\adapter\IndexController;
use app\common\JsonResponse;
class Index extends IndexController
{
    /**
     * 测试action
     * @table home_category
     * @param string $v1 参数1
     * @param string $v2 参数2
     * @return array [[ aa:bb,title,parent_id]]
     */
    public function index($v1 = '', $v2 = '')
    {
        return redirect('/reli');
        echo '<pre>';
        $argv=['snap_id'=>"5",'user_id'=>"8",'note'=>'pass'];
        $rs=call_service('topic_audit/history',$argv);
        print_r($rs);
        $argv=['snap_id'=>"5",'order'=>"asc",'note'=>'pass'];
        $rs=call_service('topic_audit/history',$argv);
        print_r($rs);

        //$rs=call_service('topic_audit/delete',$argv);
        echo '<hr><h1>end delete</h1> ';

        $argv=['user_id'=>mt_rand(1,9), 'upload_id'=>1,'url'=>'http://baidu.com','description'=>mt_rand(1,9999)];
        //$rs=call_service('topic_audit/apply',$argv) ;
        echo '<hr><h1>end apply</h1> ';
        //dump($rs);
        $argv=['snap_id'=>5,'admin_id'=>6,'note'=>'pass'];
       // $rs=call_service('topic_audit/withdraw',$argv) ;
        echo '<hr><h1>end withdraw</h1> ';
        //dump($rs);
        //$rs=call_service('topic_audit/approve',$argv) ;;
        echo '<hr><h1>end withdraw</h1> ';
    }

    function tes1(){
        echo '<pre>';
        $rs=call_service('social_friend/agree',['friend_id'=>100002,'user_id'=>1]);
        var_dump($rs);

        $rs=call_service('social_idol/follow',['idol_id'=>100002,'fans_id'=>1]);
        var_dump($rs);
        $rs2=call_service('social_idol/listIdol',['idol_id'=>1,'fans_id'=>1]);
        print_r($rs2);
        $rs2=call_service('social_fans/listfans',['idol_id'=>100002,'fans_id'=>1]);

        //print_r($rs);
        print_r($rs2);
    }
}
