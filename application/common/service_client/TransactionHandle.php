<?php

namespace app\common\service_client;

use think\Db;
use app\common\service_client\BaseHandle;

/**
 * 提供事物处理，其实这个例子不合适，不应该使用数据库事务
 */
class TransactionHandle extends BaseHandle
{

   public function __construct($config)
   {
       parent::__construct($config);
       echo static ::class;
   }

    /**
     * 执行
     */
    public function handle()
    {
        $rt = null;
        self::startTrans();
        try {
            $rt = $this->next->handle();
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
            throw $e;
        }
        return $rt;
    }


    /**
     * 开始事物
     */
    private static function startTrans()
    {

        Db::getConnection()->startTrans();
    }

    /**
     * 提交
     */
    private static function commit()
    {
        Db::getConnection()->commit();
    }

    /**
     * 回滚
     */
    private static function rollback()
    {
        Db::getConnection()->rollback();
    }
}
