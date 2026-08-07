# 市场规模与需求热度

## 核心判断

uniapp 与 PHP 开发需求无专属权威市场规模，可量化上界为 2025 年中国软件外包市场约 1.2 万亿元（估算，中国软件行业协会，含全技术栈）[1]，uniapp/PHP 为其子集。需求热度以代理指标测算：供给侧 DCloud 900 万开发者[2]、PHP 占 Web 后端 24.7%[3]；外包接单为最大路径，平台单项目客单价从猪八戒 0.5-5 千元到码市 1-5 万元[1]；模板销售与 SaaS 订阅规模更小但杠杆更高。

## 整体规模与口径

研究范围内不存在"uniapp+PHP 开发需求"的官方统计口径，需求分散在外包接单、模板销售、开源采用与招聘四类信号中。

- **规模上界**：2025 年中国软件外包市场规模突破 1.2 万亿元（估算，中国软件行业协会 2025 年度报告，年复合增长率约 12.8%，含全技术栈与全交付形态）[1]。该口径为 uniapp/PHP 外包需求可观测的规模上界，非精确值。
- **离岸口径参照**：2025 年我国企业承接离岸服务外包执行额 11688.8 亿元（跟踪统计，商务部服贸司）[1]，但以政企大客户离岸交付为主，与个人/小团队接单的 uniapp/PHP 市场结构差异较大，仅作背景。

供给侧规模方面，uniapp 生态方 DCloud 公布开发者基数 900 万、uni 统计手机端月活 10 亿[2]；PHP 生态侧，W3Techs 2024 数据显示 PHP 占 Web 后端语言份额 24.7%，Stack Overflow 2024 调查中 Laravel 在 PHP 框架采用率达 46.2%，Packagist 上 Laravel 专用扩展包超 1.8 万个[3]。

## 三条路径的需求结构

| 路径 | 可量化规模（2025） | 单价/单位 | 代表平台 | 需求热度判断 |
|---|---|---|---|---|
| 外包接单 | 上界 1.2 万亿元（全栈，估算）[1] | 程序员客栈 0.8-2 万/项、码市 1-5 万/项、猪八戒 0.5-5 千/项、实现网 150-300 元/天[1] | 猪八戒、程序员客栈、码市、实现网 | 最大、最稳定 |
| 模板销售 | 无全口径统计；头部作者月销数万元[4] | 单插件/模板数十至数千元 | DCloud 插件市场 | 较小、高杠杆 |
| SaaS 订阅 | 无可量化数据 | 订阅制 | 多为头部开源项目商业化延伸 | 起步阶段 |

外包接单为当前需求最大、可观测性最强的路径；模板销售绝对规模小但单品利润率高、可被动复购；SaaS 订阅尚处早期。DCloud 插件市场对付费插件收取 15% 服务费[4]，外包平台抽佣约 10-20%[1]，模板与接单路径的渠道成本相当。

## 细分品类需求热度

按可观测的模板下载、开源 Star 与社区接单信号，需求集中度较高的品类为：

- **电商/新零售**：likeshop 单商户开源商城在 DCloud 插件市场下载 1282、收藏 116[5]；PHP 侧 dujiaoka（独角数卡自动售货，10161 Star）反映虚拟商品交易类需求[6]。
- **即时通讯/IM**：开源热度高，对应长连接与社交外包需求。PHP 侧 workerman（11325 Star）、easywechat（10322 Star）居中文榜前列[6]；uniapp 侧 V-IM-UNIAPP 等企业级 IM 模板活跃[7]。
- **企业后台/管理**：ThinkPHP（7867 Star）、dcat-admin（3965 Star）等后台构建工具持续采用[6]。
- **本地生活/上门服务**：uniapp 插件市场出现上门按摩、预约接单等一体化模板（收藏 112）[8]，反映 O2O 垂类需求。
- **支付与微信生态 SDK**：yansongda/pay（5131 Star）、easywechat 为微信生态开发刚需底座[6]。
- **测试与安全工具**：phpunit（19825 Star）、DVWA（10932 Star）Star 居前，但属开发与演练工具，不直接映射商业外包需求[6]。

```chart
title: 主流外包平台单项目客单价区间（2025）
purpose: range
type: range
unit: 元/项
period: 2025
geography: 中国
property: 跟踪统计
source: [1]
item: 程序员客栈 | 8000 | 20000 | 0.8-2万/项 | 跟踪统计
item: 码市 | 10000 | 50000 | 1-5万/项 | 跟踪统计
item: 猪八戒 | 500 | 5000 | 500-5000元/项 | 跟踪统计
```

```chart
title: PHP 中文开源项目 Star 数对比（Gitee 中文总榜，2025-04-01）
purpose: comparison
type: bar
unit: Star
period: 2025-04-01
geography: 中文社区
property: 跟踪统计
source: [6]
item: phpunit | 19825 | 19,825 | 跟踪统计
item: workerman | 11325 | 11,325 | 跟踪统计
item: DVWA | 10932 | 10,932 | 跟踪统计
item: easywechat | 10322 | 10,322 | 跟踪统计
item: dujiaoka | 10161 | 10,161 | 跟踪统计
item: ThinkPHP | 7867 | 7,867 | 跟踪统计
item: hyperf | 6417 | 6,417 | 跟踪统计
```

## 需求热度代理指标的可用性

开源 Star、下载量、收藏数可作为需求热度代理指标，但需按品类区分：

- **业务型项目**（电商、IM、支付 SDK、企业后台）开源热度与商业外包需求**一致性较高**：workerman、easywechat、ThinkPHP 的高 Star 同时对应旺盛的微信生态与电商外包需求。
- **工具/安全/教学型项目**开源热度**不直接映射商业外包需求**：DVWA（10932 Star）为安全演练靶场，phpunit（19825 Star）为测试框架，其 Star 主要反映开发者学习与测试需求，而非接单需求。
- **垂类应用**可能出现"开源热、接单窄"：dujiaoka（自动售货）Star 居前但商业外包面较窄。
- **模板下载/收藏**比 Star 更贴近真实采购意愿：DCloud 插件市场 likeshop 下载 1282、上门按摩模板收藏 112 等可直接反映采购热度[5][8]。

综合判断：以"开源 Star + 模板下载/收藏 + 平台客单价"三指标交叉验证，可较可靠地识别高需求品类；单一 Star 指标会高估工具类、低估业务交付类需求。

> 资料边界：1.2 万亿元为全技术栈软件外包口径，uniapp/PHP 子集无独立统计，故为规模上界而非精确值；模板销售与 SaaS 订阅路径缺乏全口径统计，相关判断以代表性项目与平台公开数据近似；DCloud 900 万开发者、月活 10 亿为厂商自报跟踪统计，未经第三方核验；海外开源热度（GitHub/Packagist）与国内商业接单需求存在偏差，本报告以中文社区（Gitee、DCloud）数据为主。

## 参考资料

1. [如何做好"外包"型副业？"外包"型副业的分类及全盘思考](https://front-end.toimc.com/notes/bussiness/4-1%20%E5%A6%82%E4%BD%95%E5%81%9A%E5%A5%BD%E2%80%9C%E5%A4%96%E5%8C%85%E2%80%9D%E5%9E%8B%E5%89%AF%E4%B8%9A%EF%BC%9F%E2%80%9C%E5%A4%96%E5%8C%85%E2%80%9D%E5%9E%8B%E5%89%AF%E4%B8%9A%E7%9A%84%E5%88%86%E7%B1%BB%E5%8F%8A%E5%85%A8%E7%9B%98%E6%80%9D%E8%80%83.md) — toimc（引自中国软件行业协会 2025 年度报告、商务部服贸司），日期不详
2. [uni-app选型评估23问](https://uniapp.dcloud.net.cn/select.html) — DCloud，日期不详
3. [Laravel框架的发展前景与Composer的核心作用](https://cloud.tencent.cn/developer/article/2532881) — 腾讯云开发者社区（引自 Stack Overflow 2024、JetBrains 2023、W3Techs、Packagist），2025-06-19
4. [制作和发布插件指南](https://uniapp.dcloud.net.cn/plugin/publish.html) — DCloud，日期不详
5. [DCloud 插件市场"商城系统"搜索结果](https://ext.dcloud.net.cn/search?q=%E5%95%86%E5%9F%8E%E7%B3%BB%E7%BB%9F&page=2) — DCloud 插件市场，日期不详
6. [中文总榜 > 软件类 > PHP](https://gitee.com/exist_caipeijie/GitHub-Chinese-Top-Charts/blob/master/content/charts/overall/software/PHP.md) — Gitee（GitHub-Chinese-Top-Charts），2025-04-01
7. [V-IM-UNIAPP 手机版介绍](https://juejin.cn/post/7561264740878991414) — 掘金，2025-10-15
8. [真到位上门按摩（基于unicloud框架开发的按摩接单一体化平台）](https://ext.dcloud.net.cn/publisher?id=986966) — DCloud 插件市场，2025-10-14
