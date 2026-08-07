<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Conversation as ConvModel;
use app\model\ConversationMember;
use app\model\Group;
use app\model\Message;
use app\model\User;
use app\service\ConversationService;
use think\Request;
use think\facade\Db;

class Conversation extends BaseController
{
    /**
     * 会话列表（含未读与最后一条消息）
     */
    public function list(Request $request)
    {
        $uid = (int) $request->uid;

        $rows = Db::name('conversation_member')
            ->alias('cm')
            ->join('conversation c', 'c.id = cm.conversation_id')
            ->where('cm.user_id', $uid)
            ->field('c.id, c.type, c.group_id, c.last_message_preview, c.last_message_time, cm.unread_count')
            ->order('c.last_message_time', 'desc')
            ->select();

        $result = [];
        foreach ($rows as $row) {
            $item = $row;
            if ((int) $row['type'] === ConvModel::TYPE_SINGLE) {
                // 单聊：找到对方
                $otherId = ConversationMember::where('conversation_id', $row['id'])
                    ->where('user_id', '<>', $uid)->value('user_id');
                $peer = $otherId ? User::find($otherId) : null;
                $item['title']        = $peer->nickname ?? '';
                $item['avatar']       = $peer->avatar ?? '';
                $item['target_type']  = 'single';
                $item['target_id']    = (int) $otherId;
            } else {
                $group = Group::find($row['group_id']);
                $item['title']       = $group->name ?? '群聊';
                $item['avatar']      = $group->avatar ?? '';
                $item['target_type'] = 'group';
                $item['target_id']   = (int) $row['group_id'];
            }
            $result[] = $item;
        }

        return json_ok($result);
    }

    /**
     * 获取或创建单聊会话（需为好友）
     */
    public function createSingle(Request $request)
    {
        $uid  = (int) $request->uid;
        $toId = (int) $request->param('to_id');

        if ($toId <= 0 || $toId === $uid) {
            return json_error(400, '无效的好友');
        }
        // 校验好友关系
        $isFriend = (bool) Db::name('user_friend')
            ->where('user_id', $uid)->where('friend_id', $toId)->find();
        if (!$isFriend) {
            return json_error(403, '不是好友，无法发起会话');
        }

        $convId = (new ConversationService())->getOrCreateSingle($uid, $toId);
        return json_ok(['conv_id' => $convId]);
    }

    /**
     * 打开会话：清零未读
     */
    public function read(Request $request)
    {
        $uid    = (int) $request->uid;
        $convId = (int) $request->param('id');

        $member = ConversationMember::where('conversation_id', $convId)->where('user_id', $uid)->find();
        if (!$member) {
            return json_error(403, '不在会话中');
        }
        $member->unread_count = 0;
        $member->save();

        return json_ok(null, 'ok');
    }
}