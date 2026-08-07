import { http } from '@/utils/request.js'

// 认证
export const authApi = {
  register: (data) => http.post('/auth/register', data),
  login: (data) => http.post('/auth/login', data),
  logout: () => http.post('/auth/logout'),
}

// 用户
export const userApi = {
  me: () => http.get('/user/me'),
  update: (data) => http.put('/user/me', data),
  search: (keyword) => http.get('/user/search', { keyword }),
}

// 好友
export const friendApi = {
  list: () => http.get('/friend/list'),
  request: (data) => http.post('/friend/request', data),
  requests: () => http.get('/friend/requests'),
  handle: (data) => http.post('/friend/handle', data),
  del: (id) => http.del(`/friend/${id}`),
}

// 会话
export const conversationApi = {
  list: () => http.get('/conversation/list'),
  single: (toId) => http.post('/conversation/single', { to_id: toId }),
  read: (id) => http.post(`/conversation/${id}/read`),
}

// 消息
export const messageApi = {
  history: (convId, lastId = 0, size = 20) =>
    http.get('/message/history', { conv_id: convId, last_id: lastId, size }),
  send: (data) => http.post('/message/send', data),
}

// 群聊
export const groupApi = {
  create: (data) => http.post('/group', data),
  detail: (id) => http.get(`/group/${id}`),
  invite: (id, memberIds) => http.post(`/group/${id}/invite`, { member_ids: memberIds }),
}