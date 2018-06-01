CREATE TABLE IF NOT EXISTS `zm_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NOT NULL DEFAULT '' COMMENT '用戶昵称',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NOT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL COMMENT '用户手机',
  `login_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

CREATE TABLE IF NOT EXISTS `zm_user_visit_log`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `unique_id` VARCHAR(32) NOT NULL DEFAULT '',
  `sid` VARCHAR(32) NOT NULL DEFAULT '',
  `request_uri` VARCHAR(255) NOT NULL DEFAULT '',
  `client_ip` VARCHAR(15) NOT NULL DEFAULT '',
  `city` VARCHAR(32) NOT NULL DEFAULT '',
  `device` VARCHAR(16) NOT NULL DEFAULT '',
  `ua` VARCHAR(128) NOT NULL DEFAULT '',
  `visit_time` INT NOT NULL DEFAULT 0 COMMENT '访问时间',
  `line_time` INT NOT NULL DEFAULT 0 COMMENT '停留时间',
  `add_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '入库时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`unique_id`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kol_tags_analy`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `wbwh_uid` varchar(32) DEFAULT NULL,
  `tags` varchar(32) DEFAULT NULL,
  `cnt` int NOT NULL DEFAULT '0',
  `percent` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

