<template>
  <view class="chat">
    <!-- 消息区 -->
    <scroll-view class="msg-area" scroll-y :scroll-into-view="scrollInto" @scrolltoupper="onTop">
      <view class="msgs">
        <view
          v-for="m in messages"
          :key="m.id"
          :id="'msg-' + m.id"
          :class="['row', m.sender_id == myId ? 'mine' : 'other']"
        >
          <image v-if="m.sender_avatar" class="avatar" :src="m.sender_avatar" mode="aspectFill" />
          <view v-else class="avatar avatar-text">{{ (m.sender_nickname || '?')[0] }}</view>
          <view class="bubble">
            <text>{{ m.content }}</text>
            <text v-if="m.status === 'sending'" class="state">发送中</text>
          </view>
        </view>
      </view>
    </scroll-view>

    <!-- 输入区 -->
    <view class="input-bar">
      <input
        v-model="draft"
        class="input"
        confirm-type="send"
        placeholder="输入消息"
        @confirm="sendText"
      />
      <button class="send-btn" @click="sendText">发送</button>
    </view>
  </view>
</template>

<script>
import { useChatStore } from '@/store/chat.js'
import { messageApi } from '@/api/index.js'
import { ws } from '@/utils/ws.js'

export default {
  data() {
    return {
      conv: {},
      chatStore: useChatStore(),
      draft: '',
      myId: (uni.getStorageSync('user') || {}).id,
      scrollInto: '',
    }
  },
  computed: {
    messages() {
      return this.chatStore.messages[String(this.conv.conv_id)] || []
    },
  },
  onLoad(options) {
    this.conv = JSON.parse(decodeURIComponent(options.conv))
    uni.setNavigationBarTitle({ title: this.conv.title })
    this.chatStore.openConversation(this.conv)
    this.$nextTick(() => this.scrollBottom())
  },
  onShow() {
    // 绑定实时消息
    this._unbind = (frame) => this.chatStore.onWsMessage(frame)
    ws.on('message', this._unbind)
  },
  onHide() {
    if (this._unbind) {
      // 移除监听（简化：全局单例，由 App 统一处理，此处跳过）
    }
  },
  methods: {
    onTop() {
      this.chatStore.loadMore(this.conv.conv_id)
    },
    scrollBottom() {
      const list = this.messages
      if (list.length) {
        this.scrollInto = 'msg-' + list[list.length - 1].id
      }
    },
    sendText() {
      const content = this.draft.trim()
      if (!content) return
      this.draft = ''
      const clientMsgId = 'c' + Date.now() + '-' + Math.random().toString(16).slice(2, 8)

      // 乐观更新本地
      this.chatStore.send(this.conv.conv_id, 1, content, clientMsgId)
      this.$nextTick(() => this.scrollBottom())

      const payload = {
        type: 'send',
        conv_id: this.conv.conv_id,
        msg_type: 1,
        content,
        client_msg_id: clientMsgId,
      }
      if (ws.connected) {
        ws.send(payload)
      } else {
        // 长连接未建立时用 HTTP 兜底
        messageApi
          .send({
            conv_id: this.conv.conv_id,
            type: 1,
            content,
            client_msg_id: clientMsgId,
          })
          .then(() => this.chatStore.loadConversations())
          .catch(() => {})
      }
    },
  },
}
</script>

<style scoped>
.chat {
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: #ededed;
}
.msg-area {
  flex: 1;
  overflow: hidden;
}
.msgs {
  padding: 20rpx 30rpx;
}
.row {
  display: flex;
  margin-bottom: 30rpx;
  align-items: flex-start;
}
.row.mine {
  flex-direction: row-reverse;
}
.avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 10rpx;
  background: #b2b2b2;
  flex-shrink: 0;
}
.avatar-text {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 36rpx;
}
.bubble {
  max-width: 60%;
  margin: 0 20rpx;
  padding: 20rpx 24rpx;
  border-radius: 12rpx;
  background: #fff;
  font-size: 30rpx;
  line-height: 1.5;
  position: relative;
}
.row.mine .bubble {
  background: #95ec69;
}
.state {
  display: block;
  font-size: 20rpx;
  color: #999;
  margin-top: 6rpx;
}
.input-bar {
  display: flex;
  align-items: center;
  padding: 16rpx 20rpx;
  background: #f7f7f7;
  border-top: 1rpx solid #ddd;
  padding-bottom: env(safe-area-inset-bottom);
}
.input {
  flex: 1;
  height: 72rpx;
  background: #fff;
  border-radius: 40rpx;
  padding: 0 30rpx;
  font-size: 30rpx;
}
.send-btn {
  margin-left: 20rpx;
  background: #07c160;
  color: #fff;
  font-size: 30rpx;
  border-radius: 40rpx;
  line-height: 72rpx;
  height: 72rpx;
  padding: 0 40rpx;
}
</style>