<?php

namespace app\common\service;

use app\common\adapter\Service;

class User extends Service
{

    function get($name){
        return ['a','b'=>time(),'argv'=>$this->getArgv()];
    }

    public function setArgv($argv){
        $this->argv=$argv;
    }
}