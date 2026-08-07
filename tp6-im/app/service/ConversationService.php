<?php
declare(strict_types=1);

namespace app\service;

use app\model\Conversation;
use app\model\ConversationMember;
use think\facade\Db;

/**
 * 会话服务：负责单聊/群聊会话的创建与成员维护
 */
class ConversationService
{
    /**
     * 获取或创建单聊会话，返回会话ID
     */
    public function getOrCreateSingle(int $uidA, int $uidB): int
    {
        // 约定：较小的 uid 作为 sort key 排除方向性
        if ($uidA > $uidB) {
            [$uidA, $uidB] = [$uidB, $uidA];
        }

        // 查找两人共同存在的单聊会话
        $convId = Db::name('conversation_member')
            ->alias('m1')
            ->join('conversation_member m2', 'm1.conversation_id = m2.conversation_id')
            ->where('m1.user_id', $uidA)
            ->where('m2.user_id', $uidB)
            ->where('m1.conversation_id', 'in',
                Db::name('conversation')->where('type', Conversation::TYPE_SINGLE)->column('id'))
            ->value('m1.conversation_id');

        if ($convId) {
            return (int) $convId;
        }

        Db::startTrans();
        try {
            $conv = Conversation::create(['type' => Conversation::TYPE_SINGLE, 'group_id' => 0]);
            ConversationMember::create(['conversation_id' => $conv->id, 'user_id' => $uidA]);
            ConversationMember::create(['conversation_id' => $conv->id, 'user_id' => $uidB]);
            Db::commit();
            return (int) $conv->id;
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 创建群聊会话
     */
    public function createGroupConversation(int $groupId, array $memberIds): int
    {
        $conv = Conversation::create(['type' => Conversation::TYPE_GROUP, 'group_id' => $groupId]);

        $rows = [];
        foreach (array_unique($memberIds) as $uid) {
            $rows[] = ['conversation_id' => $conv->id, 'user_id' => (int) $uid];
        }
        if (!empty($rows)) {
            Db::name('conversation_member')->insertAll($rows);
        }
        return (int) $conv->id;
    }

    /**
     * 获取会话的所有成员ID
     */
    public function getMemberIds(int $convId): array
    {
        return ConversationMember::where('conversation_id', $convId)->column('user_id');
    }

    /**
     * 判断用户是否在会话中
     */
    public function isMember(int $convId, int $uid): bool
    {
        return (bool) ConversationMember::where('conversation_id', $convId)->where('user_id', $uid)->find();
    }
}