<template>
  <view class="contacts">
    <view class="search-bar" @click="goSearch">
      <text class="search-text">搜索</text>
    </view>

    <!-- 新的朋友 -->
    <view class="card" @click="goRequests">
      <view class="card-row">
        <view class="fc-icon">+</view>
        <text class="card-title">新的朋友</text>
        <view v-if="pendingCount > 0" class="badge">{{ pendingCount }}</view>
      </view>
    </view>

    <!-- 好友列表 -->
    <view class="card">
      <view class="card-title-block">好友 ({{ friends.length }})</view>
      <view v-for="f in friends" :key="f.id" class="friend-row" @click="chat(f)">
        <image v-if="f.avatar" class="avatar" :src="f.avatar" mode="aspectFill" />
        <view v-else class="avatar avatar-text">{{ (f.nickname || '?')[0] }}</view>
        <text class="friend-name">{{ f.nickname || f.phone }}</text>
      </view>
      <view v-if="friends.length === 0" class="empty">暂无好友</view>
    </view>
  </view>
</template>

<script>
import { friendApi, conversationApi } from '@/api/index.js'

export default {
  data() {
    return { friends: [], pendingCount: 0 }
  },
  onShow() {
    this.load()
  },
  methods: {
    async load() {
      this.friends = await friendApi.list()
      try {
        const reqs = await friendApi.requests()
        this.pendingCount = reqs.length
      } catch (e) {}
    },
    goSearch() {
      uni.navigateTo({ url: '/pages/search/search' })
    },
    goRequests() {
      uni.navigateTo({ url: '/pages/search/search?tab=requests' })
    },
    async chat(f) {
      const data = await conversationApi.single(f.id)
      uni.navigateTo({
        url: `/pages/chat/chat?conv=${encodeURIComponent(
          JSON.stringify({ conv_id: data.conv_id, type: 1, title: f.nickname, avatar: f.avatar, target_id: f.id })
        )}`,
      })
    },
  },
}
</script>

<style scoped>
.contacts {
  min-height: 100vh;
  padding: 20rpx 30rpx;
}
.search-bar {
  background: #fff;
  border-radius: 10rpx;
  padding: 20rpx;
  text-align: center;
  color: #999;
}
.card {
  background: #fff;
  border-radius: 10rpx;
  margin-top: 20rpx;
  padding: 0 30rpx;
}
.card-row {
  display: flex;
  align-items: center;
  padding: 24rpx 0;
}
.fc-icon {
  width: 60rpx;
  height: 60rpx;
  border-radius: 10rpx;
  background: #fa5151;
  color: #fff;
  font-size: 44rpx;
  text-align: center;
  line-height: 60rpx;
  margin-right: 20rpx;
}
.card-title {
  font-size: 32rpx;
}
.badge {
  margin-left: 16rpx;
  min-width: 36rpx;
  height: 36rpx;
  line-height: 36rpx;
  padding: 0 8rpx;
  border-radius: 18rpx;
  background: #fa5151;
  color: #fff;
  font-size: 22rpx;
  text-align: center;
}
.card-title-block {
  padding: 24rpx 0 10rpx;
  color: #999;
  font-size: 26rpx;
}
.friend-row {
  display: flex;
  align-items: center;
  padding: 20rpx 0;
  border-bottom: 1rpx solid #f2f2f2;
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
.friend-name {
  margin-left: 20rpx;
  font-size: 32rpx;
}
.empty {
  text-align: center;
  color: #999;
  padding: 40rpx 0;
}
</style>