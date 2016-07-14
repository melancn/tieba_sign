CREATE TABLE `sign_notes` (
 `uid` int(11) NOT NULL AUTO_INCREMENT,
 `cname` varchar(50) DEFAULT NULL,
 `cookie` text NOT NULL,
 `last_sign` date NOT NULL DEFAULT CURRENT_DATE() COMMENT '获取列表时间',
 `now_sign` time NOT NULL DEFAULT CURTIME() COMMENT '当前签到时间',
 `end_sign` date NOT NULL DEFAULT CURRENT_DATE() COMMENT '结束时间',
 `ct` int(1) NOT NULL DEFAULT '1' COMMENT 'cookie状态，2失效',
 `tnum` int(11) NOT NULL DEFAULT '0' COMMENT '列表长度',
 PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `error_code` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `code` int(10) NOT NULL,
 `usermsg` tinytext NOT NULL,
 `errmsg` tinytext NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
