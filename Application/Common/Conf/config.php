<?php
return array(
	//'配置项'=>'配置值'

    // mysql数据库配置信息
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_NAME'   => 'zhongmiao', // 数据库名
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '123456',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'zs_', // 数据库表前缀

    # url模式设定
    'URL_MODEL' => '2',

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR',
    'LOG_TYPE' => 'File'
);