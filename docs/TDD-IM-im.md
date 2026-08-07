# IM 即时通讯项目 — 技术设计文档（TDD）与开发计划

| 项目 | IM 即时通讯 |
| --- | --- |
| 版本 | v1.0 |
| 技术栈 | uniapp(Vue3+Pinia) + ThinkPHP6 + Workerman/GatewayWorker + MySQL8 + Redis |
| 日期 | 2026-08-07 |

---

## 1. 总体架构

```
客户端(uniapp: 小程序/H5/App)
   │  HTTP/HTTPS (RESTful API)        │  WSS (GatewayWorker)
   ▼                                  ▼
ThinkPHP6 应用服务 ──── GatewayWorker 长连接网关
   │  MySQL 8 / Redis                     │
 TPS 业务逻辑 (鉴权/好友/会话/群/消息)    消息路由、在线状态、广播
   │
 MySQL(持久化) + Redis(在线/未读/Token)
```

- **HTTP 层**：ThinkPHP6 提供 RESTful API（鉴权、好友、会话、群聊、消息落库、上传）。
- **实时层**：GatewayWorker 维护长连接，处理消息实时转发、在线状态、离线补偿。
- **存储层**：MySQL 持久化；Redis 缓存 Token、在线状态、未读计数。

---

## 2. 工程目录结构

### 2.1 后端（tp6-im/）
```
tp6-im/
├── app/
│   ├── common/            # 公共：JWT、响应码、上传、校验
│   ├── api/               # API 控制器
│   │   ├── controller/
│   │   │   ├── Auth.php       # 注册/登录/登出
│   │   │   ├── User.php       # 用户资料/搜索
│   │   │   ├── Friend.php     # 好友/申请
│   │   │   ├── Conversation.php # 会话列表/未读
│   │   │   ├── Group.php      # 群管理
│   │   │   ├── Message.php    # 历史消息
│   │   │   └── Upload.php     # 图片/语音上传
│   │   └── middleware/   # Token 鉴权中间件
│   ├── model/             # 数据模型
│   └── validate/          # 验证器
├── config/                # 数据库、Redis、JWT 配置
├── worker/                # GatewayWorker 启动脚本
│   ├── start.php
│   ├── Applications/      # 业务事件(Events.php)
│   └── start_*.php
└── public/
```

### 2.2 前端（uniapp-im/）
```
uniapp-im/
├── src/
│   ├── pages/            # 页面（见 PRD 页面清单）
│   ├── components/       # 消息气泡、输入栏、会话列表项等
│   ├── store/            # Pinia：user、chat、contacts
│   ├── api/              # 接口封装
│   ├── utils/            # 请求、WebSocket、鉴权
│   └── static/           # 资源
├── pages.json
├── manifest.json
└── main.js
```

---

## 3. 数据库设计

### 3.1 表清单
| 表 | 说明 |
| --- | --- |
| `user` | 用户 |
| `user_friend` | 好友关系 |
| `friend_request` | 好友申请 |
| `group` | 群 |
| `group_member` | 群成员 |
| `conversation` | 会话 |
| `conversation_member` | 会话成员 |
| `message` | 消息 |

### 3.2 核心表结构（草稿）

**user**
```sql
CREATE TABLE `user` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `phone` VARCHAR(20) NOT NULL UNIQUE COMMENT '手机号',
  `password` VARCHAR(100) NOT NULL COMMENT 'bcrypt 密文',
  `nickname` VARCHAR(50) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT '' COMMENT '头像URL',
  `signature` VARCHAR(200) DEFAULT '' COMMENT '个性签名',
  `openid` VARCHAR(64) DEFAULT '' COMMENT '预留微信登录',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_phone`(`phone`)
);
```

**user_friend**
```sql
CREATE TABLE `user_friend` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `friend_id` BIGINT UNSIGNED NOT NULL,
  `remark` VARCHAR(50) DEFAULT '' COMMENT '备注',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_friend`(`user_id`,`friend_id`)
);
```

**friend_request**
```sql
CREATE TABLE `friend_request` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `from_id` BIGINT UNSIGNED NOT NULL,
  `to_id` BIGINT UNSIGNED NOT NULL,
  `message` VARCHAR(200) DEFAULT '' COMMENT '验证消息',
  `status` TINYINT NOT NULL DEFAULT 0 COMMENT '0待处理 1同意 2拒绝',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_to`(`to_id`,`status`)
);
```

**group**
```sql
CREATE TABLE `group` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `owner_id` BIGINT UNSIGNED NOT NULL COMMENT '群主',
  `avatar` VARCHAR(255) DEFAULT '',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**group_member**
```sql
CREATE TABLE `group_member` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role` TINYINT NOT NULL DEFAULT 0 COMMENT '0成员 1群主',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_group_user`(`group_id`,`user_id`)
);
```

**conversation**
```sql
CREATE TABLE `conversation` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` TINYINT NOT NULL COMMENT '1单聊 2群聊',
  `group_id` BIGINT UNSIGNED DEFAULT 0 COMMENT '群聊时关联群',
  `last_message_id` BIGINT UNSIGNED DEFAULT 0,
  `last_message_preview` VARCHAR(255) DEFAULT '',
  `last_message_time` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**conversation_member**
```sql
CREATE TABLE `conversation_member` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conversation_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `unread_count` INT NOT NULL DEFAULT 0,
  `last_read_message_id` BIGINT UNSIGNED DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_conv_user`(`conversation_id`,`user_id`),
  KEY `idx_user`(`user_id`)
);
```

**message**
```sql
CREATE TABLE `message` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conversation_id` BIGINT UNSIGNED NOT NULL,
  `sender_id` BIGINT UNSIGNED NOT NULL,
  `type` TINYINT NOT NULL COMMENT '1文本 2图片 3语音 4系统',
  `content` TEXT COMMENT '文本内容或资源URL',
  `duration` INT DEFAULT 0 COMMENT '语音时长秒',
  `client_msg_id` VARCHAR(64) NOT NULL COMMENT '客户端去重ID',
  `status` TINYINT DEFAULT 0 COMMENT '0已入库',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_conv_time`(`conversation_id`,`id`),
  UNIQUE KEY `uk_client_msg`(`sender_id`,`client_msg_id`)
);
```

---

## 4. API 接口定义

统一响应格式：
```json
{ "code": 0, "msg": "ok", "data": {} }
```
鉴权：请求头 `Authorization: Bearer <token>`；未鉴权返回 401。

| 模块 | 方法 | 路径 | 说明 |
| --- | --- | --- | --- |
| 认证 | POST | /api/auth/register | 注册（手机号+密码） |
| 认证 | POST | /api/auth/login | 登录，返回 token+user |
| 认证 | POST | /api/auth/logout | 退出（作废 token） |
| 用户 | GET | /api/user/me | 当前用户资料 |
| 用户 | PUT | /api/user/me | 修改昵称/签名/头像 |
| 用户 | GET | /api/user/search?kw= | 搜索用户 |
| 好友 | GET | /api/friend/list | 好友列表 |
| 好友 | POST | /api/friend/request | 发送好友申请 |
| 好友 | GET | /api/friend/requests | 收到的申请列表 |
| 好友 | POST | /api/friend/handle | 同意/拒绝申请 |
| 好友 | DELETE | /api/friend/{id} | 删除好友 |
| 会话 | GET | /api/conversation/list | 会话列表（含未读） |
| 会话 | POST | /api/conversation/{id}/read | 上报已读 |
| 群 | POST | /api/group | 创建群 |
| 群 | GET | /api/group/{id} | 群详情 |
| 群 | POST | /api/group/{id}/member | 邀请入群 |
| 群 | DELETE | /api/group/{id}/member/{uid} | 退出/移除 |
| 群 | PUT | /api/group/{id} | 改群名 |
| 群 | DELETE | /api/group/{id} | 解散群 |
| 消息 | GET | /api/message/{convId}?lastId=&size= | 历史消息分页 |
| 上传 | POST | /api/upload | 图片/语音上传 |

---

## 5. GatewayWorker 实时通道设计

### 5.1 连接与鉴权
- 客户端 `wss://host:8282` 连接，首帧携带 `{type:'auth', token}`。
- 服务端校验 Token，成功后绑定 `uid`，写入 Redis 在线标记。

### 5.2 通信协议（JSON 帧）
```json
// 客户端 → 服务端
{ "type": "send", "conv_id": 1, "msg_type": 1, "content": "hi", "client_msg_id": "uuid" }
{ "type": "read", "conv_id": 1, "last_msg_id": 100 }
{ "type": "ping" }

// 服务端 → 客户端
{ "type": "message", "data": { ...消息对象 } }
{ "type": "read_ack", "conv_id": 1, "last_msg_id": 100, "by_uid": 2 }
{ "type": "pong" }
```

### 5.3 消息路由流程
1. 客户端 `send` → 服务端落库（去重校验 `client_msg_id`）。
2. 更新会话 `last_message`。
3. 查询会话所有成员，遍历推送 `message` 帧。
4. 离线成员：`unread_count+1`，上线后客户端拉历史补齐。

### 5.4 心跳与重连
- 客户端每 30s 发 `ping`，服务端 90s 无心跳判定离线。
- 断线重连：指数退避（1s/2s/4s…max 30s），重连后拉取 `last_msg_id` 之后消息。

---

## 6. 开发计划（任务拆分）

### 阶段 A：后端基础（想想）
- [ ] A1 初始化 ThinkPHP6 工程与配置（数据库/Redis/JWT）
- [ ] A2 建表（SQL 迁移脚本）
- [ ] A3 JWT 鉴权中间件 + 统一响应
- [ ] A4 注册/登录/登出接口

### 阶段 B：用户与好友
- [ ] B1 用户资料与搜索接口
- [ ] B2 好友申请/同意/列表/删除接口

### 阶段 C：会话与消息
- [ ] C1 会话创建/列表/未读接口
- [ ] C2 消息落库/历史分页接口
- [ ] C3 图片/语音上传接口

### 阶段 D：实时通道
- [ ] D1 搭建 GatewayWorker，连接鉴权与心跳
- [ ] D2 消息实时转发、已读回执、离线计数
- [ ] D3 断线重连与离线补偿

### 阶段 E：群聊
- [ ] E1 建群/群详情/改群名
- [ ] E2 邀请/退出/解散/移除
- [ ] E3 群消息广播

### 阶段 F：前端 uniapp
- [ ] F1 初始化 uniapp 工程 + Pinia + 请求/WS 封装
- [ ] F2 登录/注册页
- [ ] F3 会话列表页 + 未读角标
- [ ] F4 聊天页（文本/图片/语音、状态、已读）
- [ ] F5 通讯录页 + 搜索/添加/申请
- [ ] F6 群聊页 + 群管理
- [ ] F7 我的页（资料/退出）

### 阶段 G：联调与验收
- [ ] G1 后端单测（接口冒烟）
- [ ] G2 前后端联调（双端实时收发、离线、已读）
- [ ] G3 按 PRD 验收标准逐项验证

---

## 7. 交付里程碑
| 里程碑 | 内容 | 依赖 |
| --- | --- | --- |
| M1 | 后端基础 + 好友 + 会话 + 消息 API | A~C |
| M2 | 实时通道打通（单聊实时收发） | D + C |
| M3 | 群聊完成 | E |
| M4 | 前端核心页面完成 | F |
| M5 | 联调验收通过 | G |

---

## 8. 风险与对策
| 风险 | 说明 | 对策 |
| --- | --- | --- |
| 消息乱序/重复 | 并发发送 | 唯一 `client_msg_id` 去重 + 服务端时间序 |
| 断线丢消息 | 长连接中断 | 落库后回执 + 上线补拉 |
| 单点连接 | GatewayWorker 单机 | 多进程 + 后续集群（RegisterCenter）|
| 上传安全 | 文件类型 | 类型白名单校验、限大小 |

---

*本 TDD + 计划经确认后即可按阶段 A 开始开发。*