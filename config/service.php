<?php
/**
 * 服务配置
 */
return [
    'TopicAudit/history' =>
        [
            'cacheLifetime' => 600,//缓存时间
            'isTransOn' => false,//启用数据库事物：事务配置选项不当： 通常是库存或余额字段，不要mysql事务。
            'cachekeyHandle' => '',//自定义缓存,比如缓存文章,每1k一个集合,默认使用get_cahce_key
        ],

    'SocialFriend/listSixDegreeRelation' =>
        [
            'cacheLifetime' => 600,//缓存时间
            'isTransOn' => false,//启用数据库事物
            'cachekeyHandle' => '',//自定义缓存
        ],

    'SocialFans/sum' =>
        [
            'cacheLifetime' => 600,//缓存时间
            'isTransOn' => false,//启用数据库事物
            'cachekeyHandle' => '',//自定义缓存
        ],
];


