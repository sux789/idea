<?php

namespace app\common\adapter;
use app\common\service_client\ExecuteHandle;

class Service
{
    protected $executer;

    /**
     * 调用当前服务参数
     */
    final protected function getArgv()
    {
        return $this->executer->getFinalArgv();
    }

    /**
     * 当前$_POST中服务参数
     */
    final protected  function getArgvOnlyPost()
    {
        return array_intersect_key($this->getArgv(), $_POST);
    }

    /**
     * 注入执行者环境
     * @param ExecuteHandle $executer
     */
    final public function setExecuter(ExecuteHandle $executer)
    {
        $this->executer = $executer;
    }
}
