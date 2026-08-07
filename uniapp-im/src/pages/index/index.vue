<template>
  <view class="conv-list">
    <!-- 会话列表 -->
    <view class="list">
      <view
        v-for="c in chatStore.conversations"
        :key="c.id"
        class="item"
        @click="open(c)"
      >
        <view class="avatar">
          <image v-if="c.avatar" :src="c.avatar" mode="aspectFill" />
          <text v-else class="avatar-text">{{ (c.title || '?')[0] }}</text>
          <view v-if="c.unread_count > 0" class="badge">{{ c.unread_count }}</view>
        </view>
        <view class="info">
          <view class="row">
            <text class="name">{{ c.title }}</text>
            <text class="time">{{ c.last_message_time || '' }}</text>
          </view>
          <view class="row">
            <text class="preview">{{ c.last_message_preview || '暂无消息' }}</text>
          </view>
        </view>
      </view>

      <view v-if="!chatStore.loadingConv && chatStore.conversations.length === 0" class="empty">
        暂无会话，去通讯录找好友聊天吧
      </view>
    </view>
  </view>
</template>

<script>
import { useChatStore } from '@/store/chat.js'
import { useUserStore } from '@/store/user.js'

export default {
  data() {
    return { chatStore: useChatStore() }
  },
  onLoad() {
    if (!useUserStore().isLogin) {
      uni.reLaunch({ url: '/pages/login/login' })
      return
    }
  },
  onShow() {
    if (!useUserStore().isLogin) return
    this.chatStore.loadConversations()
  },
  onPullDownRefresh() {
    this.chatStore.loadConversations().finally(() => uni.stopPullDownRefresh())
  },
  methods: {
    open(c) {
      const conv = {
        conv_id: c.id,
        type: c.type,
        title: c.title,
        avatar: c.avatar,
        target_id: c.target_id,
      }
      uni.navigateTo({ url: `/pages/chat/chat?conv=${encodeURIComponent(JSON.stringify(conv))}` })
    },
  },
}
</script>

<style scoped>
.conv-list {
  min-height: 100vh;
}
.list {
  background: #fff;
}
.item {
  display: flex;
  align-items: center;
  padding: 24rpx 30rpx;
  border-bottom: 1rpx solid #f2f2f2;
}
.avatar {
  width: 100rpx;
  height: 100rpx;
  border-radius: 50%;
  background: #07c160;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}
.avatar image {
  width: 100%;
  height: 100%;
}
.avatar-text {
  color: #fff;
  font-size: 44rpx;
}
.badge {
  position: absolute;
  top: -6rpx;
  right: -6rpx;
  min-width: 36rpx;
  height: 36rpx;
  line-height: 36rpx;
  padding: 0 8rpx;
  border-radius: 18rpx;
  background: #fa5151;
  color: #fff;
  font-size: 22rpx;
  text-align: center;
}
.info {
  flex: 1;
  margin-left: 24rpx;
}
.row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.name {
  font-size: 32rpx;
  font-weight: 500;
}
.time {
  font-size: 24rpx;
  color: #999;
}
.preview {
  margin-top: 8rpx;
  font-size: 26rpx;
  color: #999;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.empty {
  text-align: center;
  color: #999;
  padding: 120rpx 0;
}
</style>