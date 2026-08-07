import { BASE_URL } from '@/config.js'

/**
 * 统一 HTTP 请求封装
 * - 自动携带 JWT
 * - 统一解包 { code, msg, data }
 * - 401 自动跳转登录
 */
function request(path, { method = 'GET', data = {}, auth = true } = {}) {
  const token = uni.getStorageSync('token')
  return new Promise((resolve, reject) => {
    uni.request({
      url: BASE_URL + path,
      method,
      data,
      header: {
        'Content-Type': 'application/x-www-form-urlencoded',
        ...(auth && token ? { Authorization: `Bearer ${token}` } : {}),
      },
      success: (res) => {
        const body = res.data || {}
        if (body.code === 0) {
          resolve(body.data)
        } else if (body.code === 401) {
          uni.removeStorageSync('token')
          uni.removeStorageSync('user')
          uni.reLaunch({ url: '/pages/login/login' })
          reject(body)
        } else {
          uni.showToast({ title: body.msg || '请求失败', icon: 'none' })
          reject(body)
        }
      },
      fail: (err) => {
        uni.showToast({ title: '网络异常', icon: 'none' })
        reject(err)
      },
    })
  })
}

export const http = {
  get: (path, data = {}) => request(path, { method: 'GET', data }),
  post: (path, data = {}) => request(path, { method: 'POST', data }),
  put: (path, data = {}) => request(path, { method: 'PUT', data }),
  del: (path, data = {}) => request(path, { method: 'DELETE', data }),
}