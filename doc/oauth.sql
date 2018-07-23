DROP TABLE IF EXISTS `fa_oauth`;
CREATE TABLE IF NOT EXISTS `fa_oauth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT 'OPENID',
  `header_img_url` varchar(255) DEFAULT '' COMMENT '头像链接',
  `name` varchar(200) DEFAULT '' COMMENT '昵称',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别:2=男,1=女',
  `profile_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '用户简介',
  `vote` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票数',
  `platform` enum('weibo','weixin','qq') NOT NULL DEFAULT 'weixin' COMMENT '平台:weibo=微博,weixin=微信,qq=QQ',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户授权表';