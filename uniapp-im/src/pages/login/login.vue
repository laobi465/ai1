<template>
  <view class="login">
    <view class="logo">
      <text class="logo-text">IM</text>
      <text class="slogan">即时通讯</text>
    </view>

    <view class="form">
      <view class="field">
        <input v-model="phone" type="number" maxlength="11" placeholder="请输入手机号" />
      </view>
      <view class="field">
        <input v-model="password" type="password" placeholder="请输入密码" password />
      </view>
      <button class="btn" :loading="loading" @click="onLogin">登录</button>
    </view>

    <view class="footer">
      <text class="link" @click="goRegister">注册账号</text>
    </view>
  </view>
</template>

<script>
import { useUserStore } from '@/store/user.js'

export default {
  data() {
    return { phone: '', password: '', loading: false }
  },
  methods: {
    async onLogin() {
      if (!this.phone || !this.password) {
        uni.showToast({ title: '请输入手机号和密码', icon: 'none' })
        return
      }
      this.loading = true
      try {
        await useUserStore().login({ phone: this.phone, password: this.password })
        uni.switchTab({ url: '/pages/index/index' })
      } finally {
        this.loading = false
      }
    },
    goRegister() {
      uni.navigateTo({ url: '/pages/register/register' })
    },
  },
}
</script>

<style scoped>
.login {
  min-height: 100vh;
  background: #fff;
  padding: 0 60rpx;
  display: flex;
  flex-direction: column;
}
.logo {
  margin-top: 200rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.logo-text {
  font-size: 120rpx;
  font-weight: 700;
  color: #07c160;
}
.slogan {
  margin-top: 10rpx;
  color: #999;
  font-size: 30rpx;
}
.form {
  margin-top: 100rpx;
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