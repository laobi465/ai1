<template>
  <view class="search-page">
    <!-- Tab -->
    <view class="tabs">
      <view :class="['tab', tab === 'search' ? 'active' : '']" @click="sw('search')">找人</view>
      <view :class="['tab', tab === 'requests' ? 'active' : '']" @click="sw('requests')">
        新的朋友
      </view>
    </view>

    <!-- 找人 -->
    <view v-if="tab === 'search'" class="panel">
      <view class="input-row">
        <input v-model="keyword" placeholder="手机号/昵称搜索" class="input" confirm-type="search" @confirm="doSearch" />
        <button class="btn" @click="doSearch">搜索</button>
      </view>

      <view v-for="u in results" :key="u.id" class="user-row">
        <image v-if="u.avatar" class="avatar" :src="u.avatar" mode="aspectFill" />
        <view v-else class="avatar avatar-text">{{ (u.nickname || '?')[0] }}</view>
        <view class="u-info">
          <text class="u-name">{{ u.nickname }}</text>
          <text class="u-phone">{{ u.phone }}</text>
        </view>
        <button class="add-btn" @click="add(u)">添加</button>
      </view>
      <view v-if="searched && results.length === 0" class="empty">未找到相关用户</view>
    </view>

    <!-- 好友申请 -->
    <view v-else class="panel">
      <view v-for="r in requests" :key="r.id" class="user-row">
        <image v-if="r.avatar" class="avatar" :src="r.avatar" mode="aspectFill" />
        <view v-else class="avatar avatar-text">{{ (r.nickname || '?')[0] }}</view>
        <view class="u-info">
          <text class="u-name">{{ r.nickname }}</text>
          <text class="u-phone">{{ r.message }}</text>
        </view>
        <view class="ops">
          <button class="mini-btn" @click="handle(r, 1)">同意</button>
          <button class="mini-btn refuse" @click="handle(r, 2)">拒绝</button>
        </view>
      </view>
      <view v-if="requests.length === 0" class="empty">暂无好友申请</view>
    </view>
  </view>
</template>

<script>
import { userApi, friendApi } from '@/api/index.js'

export default {
  data() {
    return { tab: 'search', keyword: '', results: [], searched: false, requests: [] }
  },
  onLoad(options) {
    if (options.tab === 'requests') {
      this.tab = 'requests'
      this.loadRequests()
    }
  },
  methods: {
    sw(t) {
      this.tab = t
      if (t === 'requests') this.loadRequests()
    },
    async doSearch() {
      if (!this.keyword.trim()) return
      this.results = await userApi.search(this.keyword.trim())
      this.searched = true
    },
    async add(u) {
      await friendApi.request({ to_id: u.id, message: '我是你的好友' })
      uni.showToast({ title: '申请已发送', icon: 'none' })
    },
    async loadRequests() {
      this.requests = await friendApi.requests()
    },
    async handle(r, status) {
      await friendApi.handle({ id: r.id, status })
      this.loadRequests()
    },
  },
}
</script>

<style scoped>
.search-page {
  min-height: 100vh;
  background: #f7f7f7;
}
.tabs {
  display: flex;
  background: #fff;
}
.tab {
  flex: 1;
  text-align: center;
  padding: 24rpx 0;
  font-size: 32rpx;
  color: #666;
}
.tab.active {
  color: #07c160;
  font-weight: 600;
  border-bottom: 4rpx solid #07c160;
}
.panel {
  padding: 20rpx 30rpx;
}
.input-row {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 10rpx;
  padding: 10rpx 20rpx;
}
.input {
  flex: 1;
  height: 70rpx;
  font-size: 30rpx;
}
.btn {
  background: #07c160;
  color: #fff;
  font-size: 28rpx;
  border-radius: 40rpx;
  line-height: 60rpx;
  height: 60rpx;
  padding: 0 30rpx;
}
.user-row {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 10rpx;
  padding: 20rpx;
  margin-top: 20rpx;
}
.avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 10rpx;
  background: #07c160;
}
.avatar-text {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 36rpx;
}
.u-info {
  flex: 1;
  margin-left: 20rpx;
  display: flex;
  flex-direction: column;
}
.u-name {
  font-size: 32rpx;
}
.u-phone {
  font-size: 24rpx;
  color: #999;
  margin-top: 6rpx;
}
.add-btn {
  background: #07c160;
  color: #fff;
  font-size: 26rpx;
  border-radius: 8rpx;
  line-height: 56rpx;
  height: 56rpx;
}
.ops {
  display: flex;
}
.mini-btn {
  font-size: 26rpx;
  border-radius: 8rpx;
  line-height: 56rpx;
  height: 56rpx;
  margin-left: 10rpx;
  background: #07c160;
  color: #fff;
}
.mini-btn.refuse {
  background: #999;
}
.empty {
  text-align: center;
  color: #999;
  padding: 80rpx 0;
}
</style>