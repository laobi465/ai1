<script>
import { useUserStore } from '@/store/user.js'
import { useChatStore } from '@/store/chat.js'
import { ws } from '@/utils/ws.js'

export default {
  onLaunch() {
    const userStore = useUserStore()
    const chatStore = useChatStore()
    // 已登录则恢复连接并绑定实时消息
    if (userStore.isLogin) {
      ws.connect(userStore.token)
      ws.on('message', (frame) => chatStore.onWsMessage(frame))
    }
  },
  onShow() {
    const userStore = useUserStore()
    if (userStore.isLogin) {
      const chatStore = useChatStore()
      chatStore.loadConversations()
    }
  },
}
</script>

<style>
/* 全局公共样式 */
page {
  background-color: #f7f7f7;
  font-size: 28rpx;
  color: #333;
}
</style>