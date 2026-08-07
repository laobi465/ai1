# macro（macro.md）

## 核心判断
PHP语言的长期支持与演进路线由PHP基金会（2025年资助11名签约开发者，贡献核心42% commits）与社区共同驱动，支持周期已延长至"2年活跃+2年安全"共4年，PHP 8.5安全支持至2029年底[1][2]；与此同时，全球与中国的合规监管双双收紧——欧盟2025年GDPR罚金超11.5亿欧元并叠加AI法案等新规[4]，中国2026年起ICP备案全面推行"人脸+实地"双重核验并新增AI/跨境差异化要求[6]，二者共同推高了PHP建站的合规运营成本与技术门槛，驱动建站服务商将合规能力转化为差异化卖点。

## 资料限制
专题稿未单列资料限制。

## 上述段落引用的参考资料
1. [Supported Versions](https://www.php.net/supported-versions.php) — PHP官方, 日期不详
2. [The PHP Foundation Impact and Transparency Report 2025](https://thephp.foundation/blog/2026/05/27/impact-and-transparency-report-2025/) — The PHP Foundation（Elizabeth Barron）, 2026-05-27
4. [GDPR, sovereignty: Why your choice of CMS matters](https://www.jahia.com/blog/gdpr-sovereignty-why-your-choice-of-cms-matters) — Jahia（Delphine Morisset）, 2026-04-30
6. [2026年ICP备案最新政策全解析：企业合规运营必看指南](https://www.netokok.com/sys-nd/165.html) — netokok.com, 2026-01-09

# market（market.md）

## 核心判断
以PHP Web框架商业软件为主要可计量口径，2025年全球PHP网站开发生态中框架软件市场规模约为38亿美元，预计至2034年以8.8%的年复合增长率扩展至81亿美元；作为行业收入更宽泛的辅助观察，2025年全球Web开发服务市场中PHP技术栈约贡献67.6亿美元收入，中国网站制作行业口径下2025年规模约为人民币720亿元。整体处于成熟存量市场维持增长、细分商业生态持续分化的阶段。

## 关键证据
2025年全球PHP Web框架软件市场价值为38亿美元，预计2034年达到81亿美元，2026—2034年预测期年复合增长率为8.8%，增长动力来自企业数字化转型持续推进、云原生部署模式普及以及电商与CMS平台的存量扩展需求[1]。该口径涵盖Laravel、Symfony、CodeIgniter、Yii、CakePHP等框架的商业授权、企业级支持服务、商业扩展包及配套托管解决方案收入。
按框架类型划分，Laravel在2025年占据PHP Web框架软件市场38.2%的收入份额，对应约14.5亿美元，其商业生态包括Livewire、Nova、Forge、Vapor等系列付费产品及全球150万活跃贡献者构成的社区商业服务网络[1]。Symfony以模块化组件和企业级治理能力锁定大型组织客户，其组件累计下载量在2026年已超过357亿次（含Laravel等下游框架的复用安装）[5]。其余CodeIgniter、Yii、CakePHP等框架合计占剩余市场。

## 资料限制
专题稿未单列资料限制。

## 上述段落引用的参考资料
1. [PHP Web Frameworks Software Market](https://dataintelo.com/report/global-php-web-frameworks-software-market) — DataIntelo，2026年3月更新
5. [Symfony vs Laravel in 2026](https://cortance.com/blog/web-development/symfony-vs-laravel-which-php-framework-should-you-build-on) — Alex Korniienko / Cortance，2026年7月3日

# chain（chain.md）

## 核心判断
PHP网站开发生态的价值集中于框架/CMS控制者所运营的商业产品链与托管支付环节：Laravel通过官方生态（Forge/Vapor/Nova）形成框架层闭环变现，Automattic通过WordPress.com托管、WooCommerce支付（WooPayments）及Jetpack/VIP企业服务掌控CMS层最大收入池，而下游建站服务商在支付分润中仅获2–5个基点的分成比例，议价能力显著弱于平台方[1][2][3][4]。

## 资料限制
专题稿未单列资料限制。

## 上述段落引用的参考资料
1. [Who Owns WordPress and How Does WordPress Make Money?](https://wp-coder.net/blog/who-owns-wordpress-and-how-does-wordpress-make-money/) — wp-coder.net，2025-11-27
2. [New Revenue Opportunities: Unlock WooPayments Revenue for New & Existing Woo Stores](https://automattic.com/for-agencies/blog/woopayments-revenue-update/) — Automattic官方博客（Mary Voelker），2025-09-23
3. [WordPress Templates Market Size, Share & Industry Analysis 2026-2032](https://pmarketresearch.com/it/wordpress-templates-market/) — PW Consulting/Evan Davis，2026-06-09
4. [创始人拒绝投资13年、仅有几十人的开源项目斩获81k+ GitHub star，用户破百万](https://m.36kr.com/p/3401127597688963) — 极客邦科技InfoQ/36氪转载，2025-07-30（引自《财富》杂志2024-09报道Laravel融资）

# competition（competition.md）

## 核心判断
PHP网站开发生态呈现**双轨制寡头分层**结构：在全球CMS市场，WordPress以近六成份额形成碾压性主导，其领先优势远超所有竞争者之和；在PHP框架市场，Laravel与Symfony构成国际市场"双强"格局，前者凭全栈生态统治快速开发场景，后者以组件化架构垄断企业级底层基座并渗透其他框架。中国市场则呈现显著本土化特征，ThinkPHP依托官方生态演进与本土开发者社群维持其独立的国产品类地位。跨语言替代方面，PHP仍以七成以上的服务器端网站占比维持存量统治，但新项目采用率面临Node.js与Python的结构性分流，替代主要发生在新建实时/API型应用而非存量CMS迁移。

## 关键证据
Laravel的统治地位建立在"约定优于配置"的开发哲学与周边生态闭环之上，其Forge（服务器运维）、Vapor（Serverless部署）、Nova（后台面板）、Octane（高性能运行时）等付费产品形成从开发到部署的完整价值链，开发者无需集成第三方工具即可完成全流程开发[1][3]。Taylor Otwell作为创始人持续推动AI集成（Laravel Boost、MCP协议）与云原生能力（Laravel Cloud），强化其在新项目中的选型粘性[1]。
Symfony的份额数据具有双层含义：直接采用率23%位居第二，但其组件（http-foundation、routing、console等约十余项）被Laravel及大量其他框架作为底层依赖，累计总下载量超过357亿次[2]。因此Symfony的实际市场地位高于直接部署数据所显示的份额——它是企业级PHP生态事实上的"基础设施供应商"，其显式架构、可配置组件与严格约定使其在需要长期维护与治理的大型系统中具备不可替代性[2]。

## 资料限制
专题稿未单列资料限制。

## 上述段落引用的参考资料
1. [The State of PHP 2025](https://blog.jetbrains.com/phpstorm/2025/10/state-of-php-2025/) — JetBrains（Tania Goral），2025年10月15日
2. [Symfony vs Laravel in 2026](https://cortance.com/blog/web-development/symfony-vs-laravel-which-php-framework-should-you-build-on) — Cortance（Alex Korniienko），2026年7月3日（引自JetBrains State of PHP 2025与BuiltWith）
3. [Laravel vs Symfony vs Other PHP Frameworks: 2026 Survey](https://www.phpeveryday.com/articles/laravel-vs-symfony-vs-other-php-frameworks-2026-survey/) — PHP Every Day，2026年4月27日（引自Packagist下载数据、JetBrains调查、GitHub活动）

# companies（companies.md）

## 核心判断
三家代表项目/公司覆盖了PHP网站开发生态的三条核心路线：全球CMS/电商生态头部（Automattic/WordPress）、PHP框架商业化典范（Laravel）、中国本土社区建站系统（Discuz!/贰道网络）。三者共同验证了同一商业规律：以开源免费的核心项目占据开发者/站长心智，再通过SaaS托管、企业级版本、云服务与生态插件市场变现，形成"开放-锁定-增值"的正反馈循环。Automattic以20年积累构筑最完整的变现矩阵（2024年收入约$710M，覆盖托管、电商支付、企业服务、安全工具）[1][4]；Laravel凭借单框架生态切入，2024年首次外部融资$57M前已靠Forge/Vapor等开发者SaaS实现自我造血[2][5]；Discuz!则在腾讯体系内外两度转型，由贰道网络接棒后以SaaS+企业定制重新激活300万站长存量[3]。三者商业模式均已得到订单/收入或多轮融资验证，其中Automattic已进入规模化盈利阶段，Laravel处于高成长扩张期，Discuz!/贰道处于从开源免费向付费服务转型的爬坡期。

```chart
title: PHP生态三家代表商业化能力对比
purpose: comparison
type: bar
unit: 规模等级
period: 2024-2025
geography: 全球/中国
property: 混合
source: [1][2][3][4][5]
item: Automattic 年收入 | 710 | $710M | 估算
item: Automattic 累计融资 | 903.6 | $903.6M | 实际
item: Laravel 首轮融资 | 57 | $57M(Accel) | 实际
item: Laravel 框架份额 | 35.87 | 35.87% PHP框架 | 跟踪统计
item: WordPress 网站份额 | 43 | ~43% 全球网站 | 跟踪统计
item: Discuz! 服务站长数 | 300 | 300万企业及站长 | 实际
```

### Automattic（WordPress / WooCommerce 母公司）

Automattic成立于2005年，总部旧金山，是围绕WordPress开源项目构建的最大商业实体[1]。WordPress本身由WordPress基金会持有商标，遵循GPL协议开源免费，Automattic通过增值服务实现商业化，其2024年收入约$710M（GetLatka估算，基于CEO访谈口径与SaaS数据库跟踪），2022年为$580.2M，2021年为$418.3M；2021–2024三年复合增长率CAGR为19.3%，2022–2024两年同比增幅为22.4%[1]。收入结构分为三部分：WordPress.com托管SaaS约占40%，WooCommerce电商生态（含WooCommerce Payments支付分成、扩展插件、专属托管）约占30%，Jetpack安全/备份工具、Akismet反垃圾、WordPress VIP企业级托管及域名邮件等增值服务合计约30%[4]。截至2025年估值维持$7.5B（2021年BlackRock/Wellington领投$288M轮时定价），累计融资$903.6M[1]。团队经历扩张后收缩：2024年约4,100人，2025年4月裁员16%后约1,700人[1]。

其行业位置是PHP网站生态的最大变现入口——WordPress支撑全球约43%的网站，WooCommerce则渗透约28%的全球电商店铺，Automattic在托管、支付、安全工具与企业服务四个环节同时卡位，任一环节的ARPU提升均可直接转化为收入[1][4]。产品路线上，Automattic近年显著加快AI功能嵌入，将AI助手、SEO自动优化等置入WordPress.com高阶套餐以拉高订阅定价，同时通过WooCommerce Payments向交易抽佣延伸，摆脱纯订阅增长的天花板[4]。商业化阶段已进入成熟期，收入结构相对多元，对单一产品线依赖度较低。关键能力在于对WordPress核心贡献权的长期控制、成熟的订阅与支付体系，以及跨公有云/自有数据中心的全球托管基础设施[1][4]。

```visual
type: comparison
title: 三家代表项目商业化矩阵
source: [1][2][3][4][5]
item: Automattic | WordPress.com托管+WooCommerce+Jetpack+VIP企业版 | 2024约$710M年收入+$7.5B估值 | 成熟规模化—开源生态+多产品SaaS矩阵
item: Laravel | Forge/Vapor/Nova/Envoyer等开发者SaaS+商业授权 | 2024 Accel $57M首轮+Forge月收入超创始人原薪资（2014起） | 高速成长—单一框架生态驱动开发者工具变现
item: Discuz!/贰道网络 | X企飞企业版+W版SaaS+应用中心+企业定制 | 300万站长存量+2024 SaaS平台上线+红杉/晨兴/谷歌早期投资 | 转型爬坡期—从免费开源向SaaS+企业服务切换
```

### Laravel 生态（Taylor Otwell 创立，Laravel LLC 运营）

Laravel由Taylor Otwell于2011年在美国阿肯色州创建，核心框架MIT协议开源免费，官方GitHub仓库截至2025年7月获得81.6k Stars与24.4k Forks，用户总数突破100万[2]。PHP框架市场份额约35.87%（SimilarTech统计），JetBrains 2024年度PHP开发者调研中61%的受访者表示经常使用Laravel，40%的初创技术公司选择其作为开发框架[2][5]。网站部署规模方面，BuiltWith 2024数据显示全球累计176.8万个网站曾使用Laravel，当前活跃站点约74.3万个，美国占32%、印度17%、巴西9%，东南亚近年增速明显[5]。

Laravel是PHP生态中"轻团队、高杠杆"商业模式的代表：创始人拒绝外部投资长达13年，2014年前Laravel框架本身完全无收入，直至Laravel Forge（服务器配置与部署SaaS）上线后1–2个月，该产品月收入即超过Otwell在货运公司的全职薪资，Otwell随即辞职专注Laravel开发，由此开启"免费框架引流—周边SaaS变现"的正循环[2]。截至2024年，Laravel LLC围绕框架发布了Forge（服务器管理）、Vapor（AWS Lambda无服务器部署）、Nova（后台管理面板授权）、Envoyer（零停机部署）、Spark（订阅计费脚手架）、Breeze/Kickstart（项目模板）等近十款付费产品，形成覆盖开发全流程的工具矩阵[2][5]。2024年Laravel完成历史上首次外部融资，由Accel领投$57M，团队规模维持在几十人，仍保持极度扁平的组织结构[2]。

其行业位置是PHP框架赛道的绝对流量入口与商业化标杆。关键能力在于：其一，框架本身的语法工程化与文档/教程体系形成开发者锁定，Laravel Bootcamp等教育产品进一步扩大心智；其二，周边SaaS工具与框架深度耦合（例如Vapor只能用于Laravel项目），形成高迁移成本下的工具链变现；其三，创始人人格化社区运营（每日90分钟处理PR、持续公开分享创业历程）构建了极强的开发者认同感[2][5]。商业化阶段处于高速成长期：Accel融资前已自我造血近十年，财务健康度较高，融资后预计用于扩充Laravel Cloud托管服务与企业级支持团队，向上切入Automattic VIP所在的企业托管市场。

### Discuz! / 合肥贰道网络科技有限公司

Discuz!产品最早发布于2001年，是中国互联网社区建站领域的标志性PHP项目，25年间累计服务300万企业及个人站长[3]。项目所有权经历三次关键转移：2007年获红杉资本、晨兴创投、谷歌风险投资；2010年整体加入腾讯，推出整合门户/博客/家园/微博功能的Discuz! X系列；2018年合肥贰道网络科技有限公司及Dismall平台创立，全权接手运营Discuz!应用中心平台并承担产品迭代发布；2022年贰道网络正式以Discuz!官方身份延续产品、应用中心、技术支持、开发与运维工作，运营团队核心成员来自腾讯Discuz!创业团队成员及外部优秀开发者[3]。

贰道网络接棒后快速推出三条变现产品线，从纯免费开源向"免费+增值"模式转型：（1）**Discuz! X企飞版**（2023年发布）面向企业与集团客户，主打企业内部文化社区与员工协同场景，提供模块化功能、集成接口与专属技术支持[3]；（2）**Discuz! W版SaaS平台**（2024年发布）面向存量百万站长群体，支持一键开启论坛与建站服务，降低自部署门槛，以订阅制收费[3]；（3）**Discuz! X5.0开源版**（2025年发布）承担生态入口职能，底层平台化改造、万象智能AI功能、分站管理、多语言支持、对象存储与集群部署能力全部内置开源版以留住开发者，付费转化则通过上层SaaS与企业定制实现[3]。此外Discuz!应用中心自2011年运营至今，为第三方开发者提供插件与模板销售分成渠道，贰道网络收取平台佣金[3]。

其行业位置是中国本土PHP社区/建站生态的存量资产盘活者——300万站长的历史积累构成独特壁垒，在垂直论坛、企业内网社区、地方门户等长尾场景中仍具不可替代性。产品路线上，2025年X5.0版本明确了"AI+平台化+集群部署"的升级方向，试图以智能化功能与企业级架构拉高付费意愿，对冲移动端社交平台对传统BBS的挤压。商业化阶段处于转型爬坡期：免费用户基础庞大但历史付费率偏低，SaaS与企业版近两年方正式推出，短期关键在于W版SaaS对存量站长的转化率，以及企飞版在集团内部社区场景的标杆案例落地。

## 资料限制
专题稿未单列资料限制。

## 上述段落引用的参考资料
1. [Automattic](https://getlatka.com/companies/automattic) — GetLatka SaaS数据库（引自2019年8月19日CEO Matt Mullenweg访谈及TechCrunch公开报道），2026年7月3日更新
2. [创始人拒绝投资13年、仅有几十人的开源项目斩获81k+ GitHub star，用户破百万，网友：他一个人拯救了PHP](https://eu.36kr.com/zh/p/3401127597688963) — 极客邦科技InfoQ（引自Laravel官方GitHub、BuiltWith 2024调查、SimilarTech/TechJury统计），2025年7月30日
3. [Discuz！团队介绍与发展历程](https://www.discuz.vip/company) — 合肥贰道网络科技有限公司（Discuz!官方运营方），日期不详
4. [How Does WordPress Make Money? WordPress Business Model](https://fourweekmba.com/how-does-wordpress-make-money/) — FourWeekMBA商业研究机构（引自Automattic公开披露、W3Techs市场份额数据），2026年3月13日发布，2026年4月更新
5. [Laravel statistics transforming PHP development](https://tms-outsource.com/blog/posts/laravel-statistics/) — TMS Outsource技术博客（引自BuiltWith 2024、JetBrains State of PHP 2024、State of Laravel 2024 Survey、Stack Overflow Developer Survey 2024），2025年8月23日
