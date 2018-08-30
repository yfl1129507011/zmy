CREATE TABLE IF NOT EXISTS `zs_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '用戶昵称',
  `password` char(32) NOT NULL COMMENT '密码',
  `phone` char(15) NOT NULL COMMENT '用户手机',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `login_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
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

CREATE TABLE IF NOT EXISTS `zs_product` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '产品名',
  `img_url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '产品图片',
  `brand_id` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `category_id` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '品类ID',
  `product_code` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '产品编码',
  `sku` INT NOT NULL DEFAULT '0' COMMENT '产品sku',
  `price` FLOAT NOT NULL DEFAULT '0' COMMENT '产品价格',
  `ingredient` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '产品成分',
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '产品表';

CREATE TABLE IF NOT EXISTS `zs_brand` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '品牌名称',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  `add_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '入库时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '品牌表';

CREATE TABLE IF NOT EXISTS `zs_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '品类名称',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  `add_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '入库时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '品类表';