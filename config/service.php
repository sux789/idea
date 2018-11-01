<?php
/**
 * 服务配置
 */
return [
    'TopicAudit/history' => [
        'cacheLifetime' => 60,//缓存时间
        'isTransOn' => false,//启用数据库事物
        'cachekeyHandle' => '',//自定义缓存,比如缓存文章,1k建议集合,没有则使用get_cahce_key函数
        'before' => [],
        'after' => [],
    ],
];
