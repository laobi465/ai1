-- ============================================================
-- IM 即时通讯 数据库结构
-- 数据库: im_db  (utf8mb4)
-- ============================================================

CREATE TABLE IF NOT EXISTS `user` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `phone` VARCHAR(20) NOT NULL COMMENT '手机号',
  `password` VARCHAR(100) NOT NULL COMMENT 'bcrypt 密文',
  `nickname` VARCHAR(50) NOT NULL COMMENT '昵称',
  `avatar` VARCHAR(255) DEFAULT '' COMMENT '头像URL',
  `signature` VARCHAR(200) DEFAULT '' COMMENT '个性签名',
  `openid` VARCHAR(64) DEFAULT '' COMMENT '预留微信登录',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_phone`(`phone`),
  KEY `idx_nickname`(`nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

CREATE TABLE IF NOT EXISTS `user_friend` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
  `friend_id` BIGINT UNSIGNED NOT NULL COMMENT '好友ID',
  `remark` VARCHAR(50) DEFAULT '' COMMENT '备注',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_friend`(`user_id`,`friend_id`),
  KEY `idx_friend`(`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='好友关系表';

CREATE TABLE IF NOT EXISTS `friend_request` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `from_id` BIGINT UNSIGNED NOT NULL COMMENT '申请人ID',
  `to_id` BIGINT UNSIGNED NOT NULL COMMENT '接收人ID',
  `message` VARCHAR(200) DEFAULT '' COMMENT '验证消息',
  `status` TINYINT NOT NULL DEFAULT 0 COMMENT '0待处理 1同意 2拒绝',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_to`(`to_id`,`status`),
  KEY `idx_from`(`from_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='好友申请表';

CREATE TABLE IF NOT EXISTS `group` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL COMMENT '群名称',
  `owner_id` BIGINT UNSIGNED NOT NULL COMMENT '群主ID',
  `avatar` VARCHAR(255) DEFAULT '' COMMENT '群头像',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_owner`(`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='群表';

CREATE TABLE IF NOT EXISTS `group_member` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `group_id` BIGINT UNSIGNED NOT NULL COMMENT '群ID',
  `user_id` BIGINT UNSIGNED NOT NULL COMMENT '成员ID',
  `role` TINYINT NOT NULL DEFAULT 0 COMMENT '0成员 1群主',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_group_user`(`group_id`,`user_id`),
  KEY `idx_user`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='群成员表';

CREATE TABLE IF NOT EXISTS `conversation` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` TINYINT NOT NULL COMMENT '1单聊 2群聊',
  `group_id` BIGINT UNSIGNED DEFAULT 0 COMMENT '群聊时关联群ID',
  `last_message_id` BIGINT UNSIGNED DEFAULT 0 COMMENT '最后一条消息ID',
  `last_message_preview` VARCHAR(255) DEFAULT '' COMMENT '会话预览',
  `last_message_time` DATETIME DEFAULT NULL,
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_group`(`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会话表';

CREATE TABLE IF NOT EXISTS `conversation_member` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conversation_id` BIGINT UNSIGNED NOT NULL COMMENT '会话ID',
  `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
  `unread_count` INT NOT NULL DEFAULT 0 COMMENT '未读条数',
  `last_read_message_id` BIGINT UNSIGNED DEFAULT 0 COMMENT '已读到的消息ID',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_conv_user`(`conversation_id`,`user_id`),
  KEY `idx_user`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会话成员表';

CREATE TABLE IF NOT EXISTS `message` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conversation_id` BIGINT UNSIGNED NOT NULL COMMENT '会话ID',
  `sender_id` BIGINT UNSIGNED NOT NULL COMMENT '发送者ID',
  `type` TINYINT NOT NULL COMMENT '1文本 2图片 3语音 4系统',
  `content` TEXT COMMENT '文本内容或资源URL',
  `duration` INT DEFAULT 0 COMMENT '语音时长(秒)',
  `client_msg_id` VARCHAR(64) DEFAULT NULL COMMENT '客户端唯一ID，可为空',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_conv_id`(`conversation_id`,`id`),
  UNIQUE KEY `uk_client_msg`(`sender_id`,`client_msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='消息表';