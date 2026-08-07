import { defineConfig } from 'vite'
import uni from '@dcloudio/vite-plugin-uni'
// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    uni(),
  ],
  server: {
    proxy: {
      // 开发环境将 /api 代理到 ThinkPHP 后端，避免跨域导致请求失败
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
      },
      // 开发环境将 /ws 代理到 Workerman WebSocket，保持同源
      '/ws': {
        target: 'ws://127.0.0.1:8282',
        ws: true,
        changeOrigin: true,
      },
    },
  },
})
