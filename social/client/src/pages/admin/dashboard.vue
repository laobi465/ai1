<template>
  <view class="admin-wrap">
    <!-- 左侧侧边菜单栏 -->
    <view class="sidebar">
      <view class="sidebar-logo">
        <text class="logo-text">社交系统管理后台</text>
      </view>
      <scroll-view class="menu-scroll" scroll-y>
        <view class="menu-group">
          <view class="menu-title">数据概览</view>
          <view class="menu-item" :class="{active: menuKey == 'dashboard'}" @click="switchMenu('dashboard')">
            <uni-icons type="statsbars" size="26"></uni-icons>
            <text>平台总数据</text>
          </view>
        </view>

        <view class="menu-group">
          <view class="menu-title">用户管理</view>
          <view class="menu-item" :class="{active: menuKey == 'userList'}" @click="switchMenu('userList')">
            <uni-icons type="person" size="26"></uni-icons>
            <text>全部用户</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'userLevel'}" @click="switchMenu('userLevel')">
            <uni-icons type="medal" size="26"></uni-icons>
            <text>会员等级配置</text>
          </view>
        </view>

        <view class="menu-group">
          <view class="menu-title">商户管理</view>
          <view class="menu-item" :class="{active: menuKey == 'merchantAudit'}" @click="switchMenu('merchantAudit')">
            <uni-icons type="checkmarkempty" size="26"></uni-icons>
            <text>商户入驻审核</text>
            <text class="badge" v-if="auditMerchantCount > 0">{{ auditMerchantCount }}</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'merchantList'}" @click="switchMenu('merchantList')">
            <uni-icons type="shop" size="26"></uni-icons>
            <text>商户列表</text>
          </view>
        </view>

        <view class="menu-group">
          <view class="menu-title">内容管理</view>
          <view class="menu-item" :class="{active: menuKey == 'articleAudit'}" @click="switchMenu('articleAudit')">
            <uni-icons type="paperplane" size="26"></uni-icons>
            <text>文章审核</text>
            <text class="badge" v-if="auditArtCount > 0">{{ auditArtCount }}</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'articleCate'}" @click="switchMenu('articleCate')">
            <uni-icons type="list" size="26"></uni-icons>
            <text>文章分类标签</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'reportList'}" @click="switchMenu('reportList')">
            <uni-icons type="flag" size="26"></uni-icons>
            <text>用户举报记录</text>
          </view>
        </view>

        <view class="menu-group">
          <view class="menu-title">广告管理</view>
          <view class="menu-item" :class="{active: menuKey == 'adPosition'}" @click="switchMenu('adPosition')">
            <uni-icons type="image" size="26"></uni-icons>
            <text>广告位配置</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'adAudit'}" @click="switchMenu('adAudit')">
            <uni-icons type="videocam" size="26"></uni-icons>
            <text>广告素材审核</text>
            <text class="badge" v-if="auditAdCount > 0">{{ auditAdCount }}</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'adSdk'}" @click="switchMenu('adSdk')">
            <uni-icons type="gear" size="26"></uni-icons>
            <text>第三方广告SDK</text>
          </view>
        </view>

        <view class="menu-group">
          <view class="menu-title">商城&资金</view>
          <view class="menu-item" :class="{active: menuKey == 'payConfig'}" @click="switchMenu('payConfig')">
            <uni-icons type="wallet" size="26"></uni-icons>
            <text>支付渠道配置</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'feeSet'}" @click="switchMenu('feeSet')">
            <uni-icons type="calculator" size="26"></uni-icons>
            <text>全局费率设置</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'withdrawBill'}" @click="switchMenu('withdrawBill')">
            <uni-icons type="download" size="26"></uni-icons>
            <text>提现账单管理</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'orderList'}" @click="switchMenu('orderList')">
            <uni-icons type="cart" size="26"></uni-icons>
            <text>平台全部订单</text>
          </view>
        </view>

        <view class="menu-group">
          <view class="menu-title">系统配置</view>
          <view class="menu-item" :class="{active: menuKey == 'smtpSet'}" @click="switchMenu('smtpSet')">
            <uni-icons type="email" size="26"></uni-icons>
            <text>SMTP邮件模板</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'switchSet'}" @click="switchMenu('switchSet')">
            <uni-icons type="toggle" size="26"></uni-icons>
            <text>审核开关配置</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'systemUpdate'}" @click="switchMenu('systemUpdate')">
            <uni-icons type="cloud-download" size="26"></uni-icons>
            <text>在线版本更新</text>
          </view>
          <view class="menu-item" :class="{active: menuKey == 'adminRole'}" @click="switchMenu('adminRole')">
            <uni-icons type="people" size="26"></uni-icons>
            <text>后台角色权限</text>
          </view>
        </view>
      </scroll-view>
    </view>

    <!-- 右侧主内容区域 -->
    <view class="main-container">
      <!-- 顶部头部栏 -->
      <view class="admin-header">
        <view class="page-title">{{ pageTitle }}</view>
        <view class="header-right">
          <view class="admin-info">
            <uni-icons type="person-filled" size="30"></uni-icons>
            <text>超级管理员</text>
          </view>
          <button class="logout-btn" @click="logout">退出登录</button>
        </view>
      </view>

      <!-- 仪表盘首页内容 -->
      <scroll-view class="content-wrap" scroll-y v-if="menuKey === 'dashboard'">
        <!-- 数据统计卡片行 -->
        <view class="data-card-row">
          <view class="data-card">
            <view class="card-top">
              <text class="card-label">平台总用户</text>
              <uni-icons type="person-filled" size="36" color="#1D39C4"></uni-icons>
            </view>
            <text class="card-num">{{ stat.userTotal }}</text>
            <text class="card-tip">今日新增 {{ stat.userToday }}</text>
          </view>
          <view class="data-card">
            <view class="card-top">
              <text class="card-label">入驻商户总数</text>
              <uni-icons type="shop" size="36" color="#00B42A"></uni-icons>
            </view>
            <text class="card-num">{{ stat.merchantTotal }}</text>
            <text class="card-tip">待审核 {{ auditMerchantCount }}</text>
          </view>
          <view class="data-card">
            <view class="card-top">
              <text class="card-label">文章总量</text>
              <uni-icons type="paperplane" size="36" color="#FF7D00"></uni-icons>
            </view>
            <text class="card-num">{{ stat.articleTotal }}</text>
            <text class="card-tip">待审核 {{ auditArtCount }}</text>
          </view>
          <view class="data-card">
            <view class="card-top">
              <text class="card-label">今日成交订单</text>
              <uni-icons type="cart-filled" size="36" color="#F53F3F"></uni-icons>
            </view>
            <text class="card-num">{{ stat.orderToday }}</text>
            <text class="card-tip">今日交易额 ¥{{ stat.amountToday }}</text>
          </view>
        </view>

        <!-- 快捷操作区域 -->
        <view class="quick-box">
          <view class="box-title">快捷操作</view>
          <view class="quick-btn-group">
            <button class="quick-btn blue" @click="switchMenu('merchantAudit')">商户审核</button>
            <button class="quick-btn orange" @click="switchMenu('articleAudit')">文章审核</button>
            <button class="quick-btn green" @click="switchMenu('withdrawBill')">导出提现账单</button>
            <button class="quick-btn purple" @click="switchMenu('systemUpdate')">系统在线更新</button>
          </view>
        </view>

        <!-- 系统状态提示 -->
        <view class="status-box">
          <view class="box-title">系统运行状态</view>
          <view class="status-item">
            <text>支付渠道：</text>
            <text class="status-green" v-if="payStatus">正常启用</text>
            <text class="status-red" v-else>通道异常</text>
          </view>
          <view class="status-item">
            <text>AI文章审核：</text>
            <text class="status-green" v-if="aiAuditSwitch">已开启</text>
            <text class="status-gray" v-else>人工审核模式</text>
          </view>
          <view class="status-item">
            <text>商户入驻审核：</text>
            <text class="status-green" v-if="merchantAuditSwitch">需人工审核</text>
            <text class="status-gray" v-else>自动通过</text>
          </view>
          <view class="status-item">
            <text>定时提现任务：</text>
            <text class="status-green">每日22:00自动生成账单</text>
          </view>
        </view>

        <!-- 最近操作日志 -->
        <view class="log-box">
          <view class="box-title">后台操作日志（超管/审核）</view>
          <view class="log-table">
            <view class="log-tr" v-for="log in logList" :key="log.id">
              <view class="log-td w200">{{ log.adminName }}</view>
              <view class="log-td w300">{{ log.operate }}</view>
              <view class="log-td w240">{{ log.createTime }}</view>
              <view class="log-td flex1">{{ log.ip }}</view>
            </view>
          </view>
        </view>
      </scroll-view>

      <!-- 其他页面占位容器 -->
      <view class="empty-page" v-else>
        <text>当前菜单：{{ pageTitle }}，业务表格/表单页面待开发</text>
      </view>
    </view>
  </view>
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'

export default {
  components: { uniIcons },
  data() {
    return {
      menuKey: "dashboard",
      pageTitle: "平台总数据仪表盘",
      // 待审核数量角标
      auditMerchantCount: 6,
      auditArtCount: 18,
      auditAdCount: 4,
      // 平台统计数据
      stat: {
        userTotal: 12586,
        userToday: 132,
        merchantTotal: 864,
        articleTotal: 36920,
        orderToday: 216,
        amountToday: 12689.60
      },
      // 系统开关状态
      payStatus: true,
      aiAuditSwitch: true,
      merchantAuditSwitch: true,
      // 操作日志模拟数据
      logList: [
        { id:1, adminName:"超级管理员", operate:"修改全局支付费率", createTime:"2026-08-08 10:22:16", ip:"192.168.1.100" },
        { id:2, adminName:"审核员01", operate:"驳回文章#36912", createTime:"2026-08-08 09:45:32", ip:"192.168.1.102" },
        { id:3, adminName:"超级管理员", operate:"执行系统在线更新", createTime:"2026-08-07 23:10:05", ip:"192.168.1.100" }
      ]
    }
  },
  methods: {
    // 切换左侧菜单
    switchMenu(key) {
      this.menuKey = key
      const titleMap = {
        dashboard:"平台总数据仪表盘",
        userList:"全部用户管理",
        userLevel:"会员等级配置",
        merchantAudit:"商户入驻审核",
        merchantList:"商户列表",
        articleAudit:"文章人工审核",
        articleCate:"文章分类标签",
        reportList:"用户举报记录",
        adPosition:"广告位配置",
        adAudit:"广告素材审核",
        adSdk:"第三方广告SDK配置",
        payConfig:"支付渠道配置",
        feeSet:"全局费率设置",
        withdrawBill:"提现账单管理",
        orderList:"平台全部订单",
        smtpSet:"SMTP邮件模板配置",
        switchSet:"审核总开关设置",
        systemUpdate:"系统在线更新",
        adminRole:"后台角色权限管理"
      }
      this.pageTitle = titleMap[key]
    },
    // 退出登录
    logout() {
      uni.showModal({
        title:"提示",
        content:"确认退出超管后台？",
        success: res => {
          if(res.confirm) uni.reLaunch({url:"/pages/admin/dashboard"})
        }
      })
    }
  }
}
</script>

<style scoped>
page {
  background-color: #f5f7fa;
  font-size: 28rpx;
  color: #1d2129;
}
.admin-wrap {
  display: flex;
  height: 100vh;
  width: 100%;
}

/* 左侧侧边栏 */
.sidebar {
  width: 480rpx;
  background-color: #111c4d;
  color: #fff;
  display: flex;
  flex-direction: column;
}
.sidebar-logo {
  height: 120rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom: 1rpx solid #243475;
}
.logo-text {
  font-size: 32rpx;
  font-weight: bold;
}
.menu-scroll {
  flex: 1;
}
.menu-group {
  margin: 30rpx 0;
}
.menu-title {
  padding: 0 32rpx;
  margin-bottom: 16rpx;
  font-size: 24rpx;
  color: #94a3c8;
}
.menu-item {
  display: flex;
  align-items: center;
  padding: 24rpx 32rpx;
  gap: 16rpx;
  position: relative;
  color: #cdd6f0;
}
.menu-item.active {
  background-color: #1D39C4;
  color: #fff;
  border-left: 8rpx solid #fff;
}
.badge {
  position: absolute;
  right: 32rpx;
  background: #f53f3f;
  border-radius: 30rpx;
  font-size: 20rpx;
  padding: 2rpx 12rpx;
}

/* 右侧主容器 */
.main-container {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.admin-header {
  height: 100rpx;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 40rpx;
  box-shadow: 0 2rpx 8rpx rgba(0,0,0,0.05);
}
.page-title {
  font-size: 34rpx;
  font-weight: 500;
}
.header-right {
  display: flex;
  align-items: center;
  gap: 40rpx;
}
.admin-info {
  display: flex;
  align-items: center;
  gap: 12rpx;
}
.logout-btn {
  padding: 12rpx 24rpx;
  border: 1rpx solid #dcdfe6;
  border-radius: 8rpx;
  background: #fff;
}
.content-wrap {
  flex: 1;
  padding: 32rpx;
}

/* 数据统计卡片 */
.data-card-row {
  display: flex;
  gap: 24rpx;
  margin-bottom: 32rpx;
}
.data-card {
  flex: 1;
  background: #fff;
  border-radius: 12rpx;
  padding: 32rpx;
  box-shadow: 0 2rpx 12rpx rgba(0,0,0,0.04);
}
.card-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20rpx;
}
.card-label {
  font-size: 26rpx;
  color: #86909c;
}
.card-num {
  display: block;
  font-size: 48rpx;
  font-weight: bold;
  color: #1d2129;
  margin-bottom: 12rpx;
}
.card-tip {
  font-size: 24rpx;
  color: #86909c;
}

/* 快捷操作模块 */
.quick-box, .status-box, .log-box {
  background: #fff;
  border-radius: 12rpx;
  padding: 32rpx;
  margin-bottom: 32rpx;
  box-shadow: 0 2rpx 12rpx rgba(0,0,0,0.04);
}
.box-title {
  font-size: 30rpx;
  font-weight: 500;
  margin-bottom: 24rpx;
}
.quick-btn-group {
  display: flex;
  gap: 20rpx;
}
.quick-btn {
  padding: 16rpx 32rpx;
  border-radius: 8rpx;
  color: #fff;
  border: none;
}
.blue {background: #1677ff;}
.orange {background: #ff7d00;}
.green {background: #00b42a;}
.purple {background: #722ed1;}

/* 系统状态行 */
.status-item {
  margin-bottom: 20rpx;
  font-size: 28rpx;
}
.status-green {color: #00b42a; margin-left: 12rpx;}
.status-red {color: #f53f3f; margin-left: 12rpx;}
.status-gray {color: #86909c; margin-left: 12rpx;}

/* 操作日志表格 */
.log-table {
  border: 1rpx solid #e5e6eb;
  border-radius: 8rpx;
}
.log-tr {
  display: flex;
  padding: 20rpx 16rpx;
  border-bottom: 1rpx solid #e5e6eb;
}
.log-tr:last-child {border-bottom: none;}
.log-td {
  padding: 0 12rpx;
}
.w200 {width: 200rpx;}
.w300 {width: 300rpx;}
.w240 {width: 240rpx;}
.flex1 {flex:1;}

/* 空白页面占位 */
.empty-page {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #86909c;
  font-size: 32rpx;
}
</style>