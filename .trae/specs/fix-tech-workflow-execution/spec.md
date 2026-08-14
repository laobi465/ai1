# 修复技术内容工作流执行 Spec

## Why
新建的“技术内容自动发布WordPress”工作流被反馈为反复无法执行。此前同类工作流已经多次通过调整 IF、HTTP 空响应和 Merge 分支尝试修复，因此需要先基于真实执行记录定位断流或失败节点，而不是继续沿用脆弱的分支编排。

## What Changes
- 调查新工作流的实际执行记录、节点输入输出和错误消息，确定无法执行的根本原因。
- 将选题、AI 生成、解析、查重、分类/标签解析、文章发布链路改为可稳定执行的数据流。
- 消除 HTTP Request 返回空数组导致下游节点没有 item、IF 节点未执行的风险。
- 简化分类、标签创建与汇总流程，避免双输入 Merge 等待未激活分支造成工作流停止。
- 保留重复文章检测；重复时不创建文章，非重复时发布带分类和标签 ID 的文章。
- 为关键边界输出可诊断的字段，便于在 n8n 执行详情中定位失败环节。

## Impact
- Affected specs: WordPress 自动发布、技术内容选题、重复检测、自动分类与标签。
- Affected code: `/workspace/tech-content-workflow.json`，以及 n8n 中工作流 `ffZb6UEyKjB7xqYN`。

## ADDED Requirements

### Requirement: 可追踪的工作流执行
系统 SHALL 在每次手动或定时运行时，让每一个必经步骤都有至少一个输入 item，直至工作流因重复跳过或完成文章发布。

#### Scenario: 非重复文章
- **WHEN** 定时触发器产生一次运行，AI 输出可解析，且 WordPress 中不存在相同 slug
- **THEN** 工作流 SHALL 完成分类/标签解析或创建，并调用 WordPress REST API 发布文章

#### Scenario: 重复文章
- **WHEN** 生成文章的 slug 已存在于已发布文章列表中
- **THEN** 工作流 SHALL 在重复检测后正常结束，且不创建第二篇相同 slug 的文章

### Requirement: 空响应安全
系统 SHALL 不把 HTTP API 返回空数组直接作为下游控制节点的唯一输入。

#### Scenario: 没有匹配术语
- **WHEN** 分类或标签查询没有匹配项
- **THEN** 工作流 SHALL 仍保留当前文章数据并继续创建所缺失的分类或标签

### Requirement: 分类和标签 ID 发布
系统 SHALL 使用 WordPress REST API 返回的数字 ID 作为 `categories` 和 `tags` 文章参数。

#### Scenario: 新分类与新标签
- **WHEN** AI 指定的分类或标签在 WordPress 中不存在
- **THEN** 工作流 SHALL 先创建术语、读取创建响应中的 ID，并以 ID 数组发布文章

### Requirement: 内容真实性约束
系统 SHALL 保持现有 DeepSeek 提示词中的技术内容真实性限制，不得弱化“不得编造”“以官方文档/官网为准”等约束。

#### Scenario: 主机测评内容
- **WHEN** 随机主题为主机测评
- **THEN** AI 提示 SHALL 要求使用选购标准、适用场景和注意事项，不得虚构价格、跑分、配置或厂商承诺

## MODIFIED Requirements

### Requirement: 工作流分支编排
工作流 SHALL 使用无需等待未执行输入分支的编排方式处理分类和标签数据，不得依赖一个分支未运行时仍会输出结果的 Merge 行为。

## REMOVED Requirements

### Requirement: 基于双分支 Merge 的术语汇总
**Reason**: 条件分支只会执行其中一个输出，双输入 Merge 可能等待另一个永远不会到达的输入，导致执行停在 Merge 前。
**Migration**: 改为每个条件路径直接输出完整文章上下文，或改为单一路径的 Code 节点汇总已创建与已有术语 ID。