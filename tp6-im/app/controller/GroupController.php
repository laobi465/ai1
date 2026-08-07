<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Conversation;
use app\model\Group;
use app\model\GroupMember;
use app\model\User;
use app\service\ConversationService;
use think\Request;
use think\facade\Db;

class GroupController extends BaseController
{
    /**
     * 创建群
     */
    public function create(Request $request)
    {
        $uid   = (int) $request->uid;
        $name  = trim((string) $request->param('name'));
        $memberIds = $request->param('member_ids', []);

        if ($name === '') {
            $name = $uid . '的群';
        }
        $memberIds = array_values(array_filter(array_map('intval', (array) $memberIds)));
        // 群主自动加入
        array_unshift($memberIds, $uid);
        $memberIds = array_values(array_unique($memberIds));

        Db::startTrans();
        try {
            $group = Group::create(['name' => $name, 'owner_id' => $uid]);

            $rows = [];
            foreach ($memberIds as $mid) {
                $rows[] = ['group_id' => $group->id, 'user_id' => $mid, 'role' => $mid === $uid ? 1 : 0];
            }
            Db::name('group_member')->insertAll($rows);

            // 创建群聊会话
            (new ConversationService())->createGroupConversation((int) $group->id, $memberIds);

            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            return json_error(500, '创建失败');
        }

        return json_ok(['id' => (int) $group->id], '创建成功');
    }

    /**
     * 群详情
     */
    public function detail(Request $request)
    {
        $uid = (int) $request->uid;
        $gid = (int) $request->param('id');

        $group  = Group::find($gid);
        if (!$group) {
            return json_error(404, '群不存在');
        }
        if (!GroupMember::where('group_id', $gid)->where('user_id', $uid)->find()) {
            return json_error(403, '不在群内');
        }

        $members = Db::name('group_member')
            ->alias('m')
            ->join('user u', 'u.id = m.user_id')
            ->where('m.group_id', $gid)
            ->field('u.id, u.nickname, u.avatar, m.role')
            ->select();

        return json_ok([
            'id'       => $group->id,
            'name'     => $group->name,
            'avatar'   => $group->avatar,
            'owner_id' => $group->owner_id,
            'members'  => $members->toArray(),
        ]);
    }

    /**
     * 邀请入群
     */
    public function invite(Request $request)
    {
        $uid = (int) $request->uid;
        $gid = (int) $request->param('id');
        $memberIds = array_map('intval', (array) $request->param('member_ids', []));

        $group = Group::find($gid);
        if (!$group) {
            return json_error(404, '群不存在');
        }
        if (!GroupMember::where('group_id', $gid)->where('user_id', $uid)->find()) {
            return json_error(403, '不在群内');
        }

        $existing = GroupMember::where('group_id', $gid)->column('user_id');
        $newIds   = array_values(array_diff($memberIds, $existing));
        if (empty($newIds)) {
            return json_error(400, '没有可邀请的新成员');
        }

        $rows = [];
        foreach ($newIds as $mid) {
            $rows[] = ['group_id' => $gid, 'user_id' => $mid, 'role' => 0];
        }
        Db::name('group_member')->insertAll($rows);

        // 将新成员加入群会话
        $conv = Conversation::where('type', Conversation::TYPE_GROUP)->where('group_id', $gid)->find();
        if ($conv) {
            $convRows = [];
            foreach ($newIds as $mid) {
                $convRows[] = ['conversation_id' => $conv->id, 'user_id' => $mid];
            }
            Db::name('conversation_member')->insertAll($convRows);
            // 系统消息
            (new \app\service\MessageService())->create((int) $conv->id, 0, 4, '有新成员加入群聊');
        }

        return json_ok(null, '邀请成功');
    }

    /**
     * 退出群 / 移除成员（群主可移除）
     */
    public function remove(Request $request)
    {
        $uid = (int) $request->uid;
        $gid = (int) $request->param('id');
        $mid = (int) $request->param('mid', 0); // 为空表示自己退出

        $group = Group::find($gid);
        if (!$group) {
            return json_error(404, '群不存在');
        }
        if (!GroupMember::where('group_id', $gid)->where('user_id', $uid)->find()) {
            return json_error(403, '不在群内');
        }

        $target = $mid ?: $uid;
        $targetRow = GroupMember::where('group_id', $gid)->where('user_id', $target)->find();
        if (!$targetRow) {
            return json_error(404, '成员不存在');
        }
        if ($target === (int) $group->owner_id) {
            return json_error(400, '群主不能退出/被移除，请先转让或解散');
        }

        GroupMember::where('group_id', $gid)->where('user_id', $target)->delete();
        $conv = Conversation::where('type', Conversation::TYPE_GROUP)->where('group_id', $gid)->find();
        if ($conv) {
            Db::name('conversation_member')->where('conversation_id', $conv->id)->where('user_id', $target)->delete();
        }
        return json_ok(null, 'ok');
    }

    /**
     * 修改群名（仅群主）
     */
    public function rename(Request $request)
    {
        $uid = (int) $request->uid;
        $gid = (int) $request->param('id');
        $name = trim((string) $request->param('name'));

        $group = Group::find($gid);
        if (!$group) {
            return json_error(404, '群不存在');
        }
        if ((int) $group->owner_id !== $uid) {
            return json_error(403, '仅群主可修改');
        }
        if ($name === '') {
            return json_error(400, '群名不能为空');
        }
        $group->name = $name;
        $group->save();
        return json_ok(null, '已修改');
    }

    /**
     * 解散群（仅群主）
     */
    public function dissolve(Request $request)
    {
        $uid = (int) $request->uid;
        $gid = (int) $request->param('id');

        $group = Group::find($gid);
        if (!$group) {
            return json_error(404, '群不存在');
        }
        if ((int) $group->owner_id !== $uid) {
            return json_error(403, '仅群主可解散');
        }

        Db::startTrans();
        try {
            $conv = Conversation::where('type', Conversation::TYPE_GROUP)->where('group_id', $gid)->find();
            if ($conv) {
                Db::name('conversation_member')->where('conversation_id', $conv->id)->delete();
                Db::name('message')->where('conversation_id', $conv->id)->delete();
                $conv->delete();
            }
            Db::name('group_member')->where('group_id', $gid)->delete();
            $group->delete();
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            return json_error(500, '解散失败');
        }
        return json_ok(null, '已解散');
    }
}