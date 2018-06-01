<?php
return array(
    //后台管理员配置
    'ADMIN_INFO' => array(
        'ADMIN' => array(
            'UID' => 1,
            'PASSWORD' => 'zhongmiao!@#$%^',
            'NICKNAME' => 'zhongmiao'
        ),
    ),
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__IMG__'    => __ROOT__ . '/Public/Back/img',
        '__CSS__'    => __ROOT__ . '/Public/Back/css',
        '__JS__'     => __ROOT__ . '/Public/Back/js',
        '__STATIC__'    => __ROOT__.'/Public/Static',
    ),

    //配置模版布局
    'LAYOUT_ON' => true,  //开启layout模版
    'LAYOUT_name' => 'layout',

    'URL_MODEL' => '1',
);