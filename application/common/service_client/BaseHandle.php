<?php

namespace app\common\service_client;

abstract class BaseHandle
{
    protected $config=[];
    protected $path = '';
    protected $argv = [];
    protected $next;

    public function __construct($config)
    {
        $this->config=$config;
        $this->path = $config['path'];
        $this->argv = $config['argv'];
        $this->cachekeyHandle = $config['cachekeyHandle'] ?? 'get_cache_key';
    }

    public function setNext($next)
    {
        $this->next = $next;
    }

    abstract public function handle();
}
