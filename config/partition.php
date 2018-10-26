<?php
/**
 * 表分区设置 [表名称=>['setp'=>分表数量,'field'=>分表字段]
 */

return
    [
        // 粉丝表,根据偶像字段idol_id去读粉丝
        'social_fans' =>
            [
                'step' => 100 * 1000,
                'field' => 'idol_id',
            ],
        'social_idol' =>
            [
                'step' => 100 * 1000,
                'field' => 'fans_id'
            ],
        'social_riend' =>
            [
                'step' => 100 * 1000,
                'field' => 'user_id'
            ],
    ];
