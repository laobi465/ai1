<template>
  <view class="profile">
    <view class="card me">
      <image v-if="user.avatar" class="avatar" :src="user.avatar" mode="aspectFill" />
      <view v-else class="avatar avatar-text">{{ (user.nickname || '?')[0] }}</view>
      <view class="me-info">
        <text class="nickname">{{ user.nickname }}</text>
        <text class="signature">{{ user.signature || '这个人很懒，什么都没写' }}</text>
      </view>
    </view>

    <view class="card">
      <view class="row">
        <text>手机号</text>
        <text class="value">{{ user.phone }}</text>
      </view>
    </view>

    <button class="logout" @click="onLogout">退出登录</button>
  </view>
</template>

<script>
import { useUserStore } from '@/store/user.js'

export default {
  data() {
    return { user: uni.getStorageSync('user') || {} }
  },
  onShow() {
    useUserStore().fetchMe().then((u) => (this.user = u)).catch(() => {})
  },
  methods: {
    onLogout() {
      uni.showModal({
        title: '提示',
        content: '确定退出登录吗？',
        success: (res) => {
          if (res.confirm) useUserStore().logout()
        },
      })
    },
  },
}
</script>

<style scoped>
.profile {
  min-height: 100vh;
  padding: 20rpx 30rpx;
}
.card {
  background: #fff;
  border-radius: 10rpx;
  padding: 30rpx;
  margin-bottom: 20rpx;
}
.me {
  display: flex;
  align-items: center;
}
.avatar {
  width: 120rpx;
  height: 120rpx;
  border-radius: 16rpx;
  background: #07c160;
}
.avatar-text {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 52rpx;
}
.me-info {
  margin-left: 30rpx;
  display: flex;
  flex-direction: column;
}
.nickname {
  font-size: 38rpx;
  font-weight: 600;
}
.signature {
  margin-top: 10rpx;
  color: #999;
  font-size: 26rpx;
}
.row {
  display: flex;
  justify-content: space-between;
  padding: 20rpx 0;
}
.value {
  color: #999;
}
.logout {
  margin-top: 40rpx;
  background: #fff;
  color: #fa5151;
  border-radius: 10rpx;
}
</style>