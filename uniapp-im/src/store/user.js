import { defineStore } from 'pinia'
import { authApi, userApi } from '@/api/index.js'
import { ws } from '@/utils/ws.js'

export const useUserStore = defineStore('user', {
  state: () => ({
    token: uni.getStorageSync('token') || '',
    user: uni.getStorageSync('user') || null,
  }),
  getters: {
    isLogin: (s) => !!s.token,
  },
  actions: {
    async login(payload) {
      const data = await authApi.login(payload)
      this._set(data)
    },
    async register(payload) {
      const data = await authApi.register(payload)
      this._set(data)
    },
    _set(data) {
      this.token = data.token
      this.user = data.user
      uni.setStorageSync('token', data.token)
      uni.setStorageSync('user', data.user)
      // 建立实时连接
      ws.connect(data.token)
    },
    async fetchMe() {
      this.user = await userApi.me()
      uni.setStorageSync('user', this.user)
    },
    logout() {
      ws.close()
      this.token = ''
      this.user = null
      uni.removeStorageSync('token')
      uni.removeStorageSync('user')
      uni.reLaunch({ url: '/pages/login/login' })
    },
  },
})