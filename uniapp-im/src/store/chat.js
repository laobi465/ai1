import { defineStore } from 'pinia'
import { conversationApi, messageApi } from '@/api/index.js'
import { ws } from '@/utils/ws.js'

export const useChatStore = defineStore('chat', {
  state: () => ({
    conversations: [],
    // 当前打开的会话：{ conv_id, type, title, avatar, target_id }
    current: null,
    // key: conv_id => 消息数组
    messages: {},
    loadingConv: false,
  }),
  actions: {
    async loadConversations() {
      this.loadingConv = true
      try {
        this.conversations = await conversationApi.list()
      } finally {
        this.loadingConv = false
      }
    },
    async openConversation(conv) {
      this.current = conv
      const key = String(conv.conv_id)
      if (!this.messages[key]) {
        try {
          const res = await messageApi.history(conv.conv_id)
          this.messages[key] = res.list || []
        } catch (e) {
          this.messages[key] = []
        }
      }
      // 清零未读
      conversationApi.read(conv.conv_id).catch(() => {})
      this._markRead(conv.conv_id)
    },
    async loadMore(convId) {
      const key = String(convId)
      const list = this.messages[key] || []
      const lastId = list.length ? list[0].id : 0
      const res = await messageApi.history(convId, lastId, 20)
      if (res.list && res.list.length) {
        this.messages[key] = [...res.list, ...list]
      }
      return res.has_more
    },
    // 本地发送（乐观更新），随后通过 WS/HTTP 真正发送
    send(convId, type, content, clientMsgId) {
      const key = String(convId)
      const user = uni.getStorageSync('user') || {}
      const temp = {
        id: `temp-${clientMsgId}`,
        conversation_id: convId,
        sender_id: user.id,
        type,
        content,
        client_msg_id: clientMsgId,
        create_time: new Date().toISOString().replace('T', ' ').slice(0, 19),
        sender_nickname: user.nickname,
        sender_avatar: user.avatar,
        status: 'sending',
      }
      if (!this.messages[key]) this.messages[key] = []
      this.messages[key].push(temp)
      return temp
    },
    // WS 收到新消息
    onWsMessage(frame) {
      if (frame.type === 'message') {
        const msg = frame.data
        const key = String(msg.conversation_id)
        if (!this.messages[key]) this.messages[key] = []
        // 去重（替换本地临时消息）
        const idx = this.messages[key].findIndex(
          (m) => m.client_msg_id && m.client_msg_id === msg.client_msg_id
        )
        if (idx >= 0) {
          this.messages[key].splice(idx, 1, msg)
        } else {
          this.messages[key].push(msg)
        }
        // 刷新会话列表，命中未读
        this.loadConversations()
      } else if (frame.type === 'read_ack') {
        // 已读回执，可用来更新对端已读角标
      }
    },
    _markRead(convId) {
      const item = this.conversations.find((c) => Number(c.id) === Number(convId))
      if (item) item.unread_count = 0
    },
  },
})

// 注册 WS 消息回调（在 App onLaunch 建立连接后调用）
export function bindWs() {
  ws.on('message', () => {
    // 由页面轮询调用 store.onWsMessage，避免 store 循环依赖
  })
}