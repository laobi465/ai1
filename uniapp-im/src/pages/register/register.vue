<template>
  <view class="register">
    <view class="title">注册账号</view>
    <view class="form">
      <view class="field">
        <input v-model="phone" type="number" maxlength="11" placeholder="请输入手机号" />
      </view>
      <view class="field">
        <input v-model="nickname" type="text" placeholder="请输入昵称（选填）" />
      </view>
      <view class="field">
        <input v-model="password" type="password" placeholder="请输入密码（6-32位）" password />
      </view>
      <button class="btn" :loading="loading" @click="onRegister">注册</button>
    </view>
    <view class="footer">
      <text class="link" @click="goBack">已有账号，去登录</text>
    </view>
  </view>
</template>

<script>
import { useUserStore } from '@/store/user.js'

export default {
  data() {
    return { phone: '', nickname: '', password: '', loading: false }
  },
  methods: {
    async onRegister() {
      if (!this.phone || !this.password) {
        uni.showToast({ title: '请输入手机号和密码', icon: 'none' })
        return
      }
      this.loading = true
      try {
        await useUserStore().register({
          phone: this.phone,
          password: this.password,
          nickname: this.nickname,
        })
        uni.switchTab({ url: '/pages/index/index' })
      } finally {
        this.loading = false
      }
    },
    goBack() {
      uni.navigateBack()
    },
  },
}
</script>

<style scoped>
.register {
  min-height: 100vh;
  background: #fff;
  padding: 0 60rpx;
}
.title {
  padding-top: 150rpx;
  font-size: 48rpx;
  font-weight: 700;
  margin-bottom: 80rpx;
}
.field {
  border-bottom: 1rpx solid #eee;
  margin-bottom: 40rpx;
  padding: 10rpx 0;
}
.field input {
  height: 80rpx;
  font-size: 32rpx;
}
.btn {
  margin-top: 40rpx;
  background: #07c160;
  color: #fff;
  border-radius: 50rpx;
  font-size: 34rpx;
}
.footer {
  margin-top: 60rpx;
  text-align: center;
}
.link {
  color: #07c160;
  font-size: 30rpx;
}
</style>