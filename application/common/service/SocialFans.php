<?php

namespace app\common\service;

use app\common\adapter\Service;
use app\common\model\SocialFans as SocialFansModel;

/**
 * 关注的粉丝表,用于发送关注信息给粉丝
 */
class SocialFans extends Service
{
    protected $modelSocialFans;

    public function __construct(SocialFansModel $modelSocialFans)
    {
        $this->modelSocialFans = $modelSocialFans;
    }

    /**
     * 统计偶像和粉丝数目
     * - 为文档举例
     * @return array [idol_count关注人数,fans_count粉丝人数]
     */
    function sum(int $user_id = 1)
    {
        $sql = "
        WITH 
        t_count_1 AS 
            ( SELECT COUNT( idol_id ) AS idol_count, 0 AS fans_count 
            FROM social_idol 
            WHERE fans_id=$user_id
            ) ,
        t_count_2 AS 
            ( SELECT 0 AS idol_count, COUNT( fans_id ) AS fans_count 
            FROM  social_fans
            WHERE idol_id=$user_id
            ) ,
        t_total AS 
            (   SELECT * from t_count_1 
                UNION ALL 
                SELECT * from t_count_2
            )
        SELECT sum(idol_count) as idol_count, sum(fans_count) as fans_count 
        FROM t_total 
        ";
        return $this->modelSocialFans->query($sql);
    }
}