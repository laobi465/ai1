<?php
declare(strict_types=1);

namespace app\service;

use app\model\ConversationMember;
use app\model\Message;
use think\facade\Db;

/**
 * 消息服务：落库、更新会话、维护未读计数
 */
class MessageService
{
    /**
     * 保存消息并维护会话与未读
     * @return int 消息ID
     */
    public function create(int $convId, int $senderId, int $type, string $content, int $duration = 0, string $clientMsgId = ''): int
    {
        // 去重：同一发送者同一 client_msg_id 只落一次
        if ($clientMsgId !== '') {
            $exists = Message::where('sender_id', $senderId)->where('client_msg_id', $clientMsgId)->find();
            if ($exists) {
                return (int) $exists->id;
            }
        }

        $preview = $this->buildPreview($type, $content);

        Db::startTrans();
        try {
            $msg = Message::create([
                'conversation_id' => $convId,
                'sender_id'       => $senderId,
                'type'            => $type,
                'content'         => $content,
                'duration'        => $duration,
                'client_msg_id'   => $clientMsgId !== '' ? $clientMsgId : null,
            ]);

            // 更新会话最后消息
            Db::name('conversation')->where('id', $convId)->update([
                'last_message_id'     => $msg->id,
                'last_message_preview' => $preview,
                'last_message_time'   => date('Y-m-d H:i:s'),
            ]);

            // 除发送者外，其他成员未读 +1
            Db::name('conversation_member')
                ->where('conversation_id', $convId)
                ->where('user_id', '<>', $senderId)
                ->inc('unread_count')
                ->update();

            Db::commit();
            return (int) $msg->id;
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 生成会话预览文本
     */
    public function buildPreview(int $type, string $content): string
    {
        return match ($type) {
            Message::TYPE_IMAGE => '[图片]',
            Message::TYPE_VOICE => '[语音]',
            Message::TYPE_SYSTEM => '[系统消息]',
            default => mb_substr($content, 0, 50),
        };
    }
}