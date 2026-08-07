<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Message;
use app\model\User;
use app\service\ConversationService;
use app\service\MessageService;
use think\Request;

class MessageController extends BaseController
{
    /**
     * 历史消息分页（按 id 倒序，向下翻页）
     */
    public function history(Request $request)
    {
        $uid    = (int) $request->uid;
        $convId = (int) $request->param('conv_id');
        $lastId = (int) $request->param('last_id', 0); // 已加载到的最新消息ID
        $size   = (int) $request->param('size', 20);
        $size   = min(max($size, 1), 50);

        if (!(new ConversationService())->isMember($convId, $uid)) {
            return json_error(403, '不在会话中');
        }

        $query = Message::where('conversation_id', $convId);
        if ($lastId > 0) {
            $query->where('id', '<', $lastId);
        }
        $msgs = $query->order('id', 'desc')->limit($size)->select()->toArray();
        $msgs = array_reverse($msgs);

        // 附带发送者信息
        $senders = User::column('nickname,avatar', 'id');

        return json_ok([
            'list'     => $this->decorate($msgs, $senders),
            'has_more' => count($msgs) === $size,
        ]);
    }

    /**
     * HTTP 发送消息入口（长连接未建立时兜底）
     */
    public function send(Request $request)
    {
        $uid    = (int) $request->uid;
        $convId = (int) $request->param('conv_id');
        $type   = (int) $request->param('type', Message::TYPE_TEXT);
        $content = trim((string) $request->param('content', ''));
        $duration = (int) $request->param('duration', 0);
        $clientMsgId = (string) $request->param('client_msg_id', '');

        if (!(new ConversationService())->isMember($convId, $uid)) {
            return json_error(403, '不在会话中');
        }
        if (!in_array($type, [Message::TYPE_TEXT, Message::TYPE_IMAGE, Message::TYPE_VOICE], true)) {
            return json_error(400, '不支持的消息类型');
        }
        if ($content === '') {
            return json_error(400, '消息内容不能为空');
        }

        $msgId = (new MessageService())->create($convId, $uid, $type, $content, $duration, $clientMsgId);
        $msg   = Message::find($msgId);

        return json_ok($msg, 'ok');
    }

    private function decorate(array $msgs, array $senders): array
    {
        foreach ($msgs as &$m) {
            $m['sender_nickname'] = $senders[$m['sender_id']]['nickname'] ?? '';
            $m['sender_avatar']   = $senders[$m['sender_id']]['avatar'] ?? '';
        }
        return $msgs;
    }
}