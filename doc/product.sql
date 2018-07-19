DROP TABLE IF EXISTS `fa_product`;
CREATE TABLE IF NOT EXISTS `fa_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `oauth_id` int(10) NOT NULL DEFAULT '0' COMMENT '授权ID',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
  `status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '状态值:0=审核中,1=未通过,2=已通过',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='作品表';