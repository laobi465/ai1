# 新工作流：面向外网 word.apexck.com 的技术内容自动发布

## 摘要
新建一个 n8n 工作流（与现有"热点发布"工作流**相互独立**），面向**同一个** WordPress 站点 `https://word.apexck.com`，按**每 6 小时 1 篇**的频率，从 6 个固定主题（开源程序教程 / 主机测评 / 服务器运维 / WordPress优化 / 在线工具教程 / Python/PHP 入门）中**随机选一个**，由 DeepSeek 生成一篇准确、真实、SEO/GEO 优化的文章，自动创建/复用分类与标签，查重后发布。

## 当前状态分析
- 线上已部署的工作流（n8n workflow id `TkcwnbbgpwgTA3tE`，versionCounter 17）是"抓取今日头条热点+DeepSeek+自动分类标签+发布"，其**分类/标签 ID 解析与创建链路**（HTTP 获取分类/标签 → Code 解析术语ID → IF 分支创建/保留 → Merge → 组装发布参数 → HTTP 创建文章）已经验证可靠，是本次复用的模板。
- 已确认：
  - WordPress REST API 各端点可用（categories 现有 `未分类` id=1；tags 为空；posts 有 3 篇已发布）。
  - 凭据：WordPress `wordpressApi`（id `qsJwYYosAjqF2zc9`）、DeepSeek `deepSeekApi`（id `SgrYhV32MeVZERum`）均已存在，直接复用。
  - 老问题教训：查重 HTTP 必须返回非空（站点已有文章，满足）；分类/标签必须传 **ID** 而非名称。

## 方案设计（新工作流节点）
交付一个本地 JSON 文件 `workspace/tech-content-workflow.json`，包含 name、nodes、connections、settings，通过 n8n Public API `POST /api/v1/workflows` 创建为**新工作流**。

### 节点清单与说明
1. **Schedule Trigger** — 每 6 小时触发（`hoursInterval: 6`）。
2. **Code - 选题** — 内置 6 主题数组（category + instruction），`Math.random()` 随机选 1 个，输出 `{ category, instruction }`。主题定义：
   - `开源程序教程`：选一款知名开源程序写安装/配置/使用教程。
   - `主机测评`：写主机/虚拟主机/VPS 测评或选购指南；**强制真实**：不编造价格/跑分/配置/厂商承诺，具体参数注明"以官方文档/官网为准"，重点写选购标准、适用场景、注意事项。
   - `服务器运维`：Linux 常用命令、巡检、安全加固、备份、日志等实战教程。
   - `WordPress优化`：性能/缓存/SEO/安全/插件等优化教程。
   - `在线工具教程`：选一个实用在线工具介绍功能/用法/优缺点。
   - `Python/PHP入门`：环境搭建/基础语法/常用库/框架/小实战。
3. **AI Agent** — 输入 `={{ $json.instruction }}`。systemMessage（中文）明确要求：
   - 在给定主题内选一个**具体、可写**的子题；
   - 内容**原创、客观、真实、可验证**；只写有把握的事实；不编造版本号/价格/性能数据/API 细节；不确定处用"以官方文档/官网为准"表述；主机测评尤其不得虚构；
   - SEO/GEO 优化：标题有吸引力、正文含 h2 小标题/段落/列表、自然融入关键词；
   - 输出**严格 JSON**（不要 markdown 代码块/额外文字）：`{"title","slug","excerpt","content(HTML)","category","tags[3-5]"}`
4. **DeepSeek Chat Model** — 复用 `deepSeekApi`。
5. **Code - 解析AI输出** — 复用现有健壮解析器（剥 markdown 代码块、截取首尾 `{}`、`JSON.parse`、slug 兜底生成），输出 `{title,slug,excerpt,content,category,tags}`。
6. **HTTP Request - 查重** — `GET https://word.apexck.com/wp-json/wp/v2/posts?per_page=100&_fields=slug&status=publish`。
7. **Code - 判断重复** — `$input.all()` 比对 slug，引用 `Code - 解析AI输出`，输出 `{...art, isDuplicate}`。
8. **IF - 是否重复** — `isDuplicate == true` → 输出[0] 结束；否则 → 输出[1] 进入发布链路。
9. **发布链路（复用已验证模板）**：
   - HTTP - 获取分类（GET categories?per_page=100&_fields=id,name）
   - HTTP - 获取标签（GET tags?per_page=100&_fields=id,name）
   - Code - 解析术语ID（计算 categoryId / existingTagIds / missingCategoryName / missingTagNames）
   - IF - 分类缺失（notEmpty）→ 缺失：HTTP - 创建分类 → Code - 注入分类ID → Merge-分类[0]；存在：Code - 保留分类ID → Merge-分类[1]
   - Merge - 分类（combineByPosition）
   - IF - 标签缺失（missingTagNames.length>0）→ 缺失：Code - 展开标签 → HTTP - 创建标签 → Code - 合并标签ID → Merge-标签[0]；否则：Code - 保留标签 → Merge-标签[1]
   - Merge - 标签（combineByPosition）
   - Code - 组装发布参数（`{title,content,slug,excerpt,status:'publish',comment_status:'open',categories:[categoryId 或 1],tags:[finalTagIds]}`）
   - HTTP - 创建文章（POST wp-json/wp/v2/posts，`jsonBody = JSON.stringify($json)`，`authentication: predefinedCredentialType` + `wordpressApi`）

### 连接关系
与线上已部署工作流（TkcwnbbgpwgTA3tE）的 connections 完全一致，仅把 `Schedule Trigger → HTTP Request(热点)` 改为 `Schedule Trigger → Code-选题 → AI Agent`。

## 假设与决策
- 目标站点 = `word.apexck.com`，复用现有 `wordpressApi` 与 `deepSeekApi` 凭据，不新建凭据。
- 选题方式 = 6 主题随机（`Math.random()`），不维护子题清单（跟随用户选择"随机/轮换"）。
- 频率 = 每 6 小时 1 篇（Schedule Trigger hoursInterval=6）。
- 准确性要求 = 通过 AI systemMessage 的"真实、不编造、以官方为准"约束实现；如需更强保障可在后续加"发布前人工审核"或"二次校验"节点，本次不加入（避免过度设计）。
- 部署方式 = n8n Public API 创建新工作流；激活需用户在编辑器手动开启（API 无法远程激活，沿用既有结论）。

## 验证步骤
1. `POST /api/v1/workflows` 创建成功后，`GET /api/v1/workflows/{newId}` 确认节点数 = 25、`versionCounter` 有值、name 正确。
2. 确认新工作流与旧工作流 ID 不同（相互独立）。
3. 用户在 n8n 编辑器打开新工作流，点 **Execute workflow** 手动跑一次，确认整条链路（选题→DeepSeek→解析→查重→分类/标签→发布）成功且文章已发布。
4. 发布后到 `https://word.apexck.com` 确认文章可见、分类/标签已正确创建。
5. 确认无误后用户开启 **Active** 开关，进入每 6 小时自动发布。