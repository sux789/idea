<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/29
 */

namespace app\common\service\admin;

use app\common\adapter\Service;
use app\common\model\AdminUser as AdminUserModel;

class AdminUser extends Service
{
    protected $modelAdminUser;

    public function __construct(AdminUserModel $modelAdminUser)
    {
        $this->modelAdminUser = $modelAdminUser;
    }

    /**
     * 根据手机号读取
     * @return array
     */
    function find($mobile)
    {
        $where = [
            'mobile_id' => mobile_to_id($mobile),
            'mobile' => $mobile,
        ];
        return $this->modelAdminUser->get($where);
    }

    /**
     * 添加
     */
    function save($mobile_id, $mobile, $name, $password)
    {
        $argv = $this->getArgv();
        return $this->modelAdminUser->insert($argv);
    }

    /**
     * 手机id是否存在
     */
    function exists($mobile_id)
    {
        $argv = $this->getArgv();
        return $this->modelAdminUser->get($argv);
    }


}