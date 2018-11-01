<?php

namespace app\common\service_client;

use think\Db;
use app\common\service_client\BaseHandle;

class TransactionHandle extends BaseHandle
{

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
