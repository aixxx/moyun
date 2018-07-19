DROP TABLE IF EXISTS `fa_votelog`;
CREATE TABLE IF NOT EXISTS `fa_votelog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `oauthid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票人ID',
  `oauthpid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被投票ID',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='投票记录表';