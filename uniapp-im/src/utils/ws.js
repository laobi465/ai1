import { WS_URL } from '@/config.js'

/**
 * WebSocket 封装：连接、鉴权、心跳、断线重连（指数退避）
 * 事件回调：onMessage(fn)、onOpen(fn)、onClose(fn)
 */
export class WS {
  constructor() {
    this.socket = null
    this.connected = false
    this.authenticated = false
    this.handlers = { message: [], open: [], close: [] }
    this.reconnectAttempts = 0
    this.manualClose = false
    this.heartbeatTimer = null
  }

  on(event, fn) {
    if (this.handlers[event]) this.handlers[event].push(fn)
  }

  emit(event, payload) {
    this.handlers[event].forEach((fn) => fn(payload))
  }

  connect(token) {
    this.token = token
    this.manualClose = false
    this._open()
  }

  _open() {
    if (this.socket) this.socket.close()
    this.socket = uni.connectSocket({
      url: WS_URL,
      complete: () => {},
    })

    this.socket.onOpen(() => {
      this.connected = true
      this.reconnectAttempts = 0
      this.emit('open')
      // 鉴权
      this.send({ type: 'auth', token: this.token })
      this._startHeartbeat()
    })

    this.socket.onMessage((res) => {
      let frame
      try {
        frame = JSON.parse(res.data)
      } catch (e) {
        return
      }
      if (frame.type === 'auth_ok') {
        this.authenticated = true
      } else if (frame.type === 'auth_fail') {
        this.manualClose = true
        this.close()
        uni.removeStorageSync('token')
        uni.reLaunch({ url: '/pages/login/login' })
        return
      }
      this.emit('message', frame)
    })

    this.socket.onClose(() => {
      this.connected = false
      this.authenticated = false
      this.emit('close')
      this._stopHeartbeat()
      if (!this.manualClose) this._reconnect()
    })

    this.socket.onError(() => {
      // onClose 会触发重连
    })
  }

  _reconnect() {
    const delay = Math.min(30000, Math.pow(2, this.reconnectAttempts) * 1000)
    this.reconnectAttempts += 1
    setTimeout(() => {
      if (!this.manualClose) this._open()
    }, delay)
  }

  _startHeartbeat() {
    this._stopHeartbeat()
    this.heartbeatTimer = setInterval(() => {
      if (this.connected) this.send({ type: 'ping' })
    }, 30000)
  }

  _stopHeartbeat() {
    if (this.heartbeatTimer) {
      clearInterval(this.heartbeatTimer)
      this.heartbeatTimer = null
    }
  }

  send(data) {
    if (this.socket && this.connected) {
      this.socket.send({ data: JSON.stringify(data) })
    }
  }

  close() {
    this.manualClose = true
    this._stopHeartbeat()
    if (this.socket) this.socket.close()
  }
}

export const ws = new WS()