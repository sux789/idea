<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/29
 */

namespace app\common\service\admin;

use app\common\adapter\Service;
use app\common\model\AdminUser;

class User extends Service
{
    protected $adminUser;

    public function __construct(AdminUser $adminUser)
    {
        $this->adminUser = $adminUser;
    }

    /**
     * 根据手机号读取
     * @param $mobile
     * @param $password
     */
    function find($mobile)
    {
        return $this->adminUser
            ->where(['mobile_id'=>get_mobile_id($mobile)])
            ->find();
    }

    /**
     * 添加
     */
    function add($mobile_id, $mobile, $name, $password)
    {
        $argv = $this->getArgv();
        $argv['create_at'] = time();
        return $this->adminUser->insertGetId($argv);
    }

    /**
     * 手机id是否存在
     */
    function exists($mobile_id)
    {
        return $this->adminUser->whereExists(['mobile_id' => $mobile_id]);
    }


}