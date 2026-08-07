// 全局配置
// 开发环境走 Vite 代理（同源），避免跨域 "Network error"；生产环境改为后端绝对地址
export const BASE_URL = '/api'

// H5 下 WebSocket 走同源 Vite 代理（/ws -> 127.0.0.1:8282），避免跨域连接失败
export const WS_URL =
  typeof location !== 'undefined' ? `ws://${location.host}/ws` : 'ws://127.0.0.1:8282'