<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Friend as FriendModel;
use app\model\FriendRequest;
use app\model\User;
use think\Request;
use think\facade\Db;

class Friend extends BaseController
{
    /**
     * 好友列表
     */
    public function list(Request $request)
    {
        $uid = (int) $request->uid;
        $rows = Db::name('user_friend')
            ->alias('f')
            ->join('user u', 'u.id = f.friend_id')
            ->where('f.user_id', $uid)
            ->field('u.id, u.phone, u.nickname, u.avatar, u.signature, f.remark')
            ->order('f.create_time', 'desc')
            ->select();

        return json_ok($rows->toArray());
    }

    /**
     * 发送好友申请
     */
    public function request(Request $request)
    {
        $uid     = (int) $request->uid;
        $toId    = (int) $request->param('to_id');
        $message = trim((string) $request->param('message', ''));

        if ($toId <= 0 || $toId === $uid) {
            return json_error(400, '无效的好友');
        }
        if (!User::find($toId)) {
            return json_error(404, '用户不存在');
        }
        if (FriendModel::where('user_id', $uid)->where('friend_id', $toId)->find() ||
            FriendModel::where('user_id', $toId)->where('friend_id', $uid)->find()) {
            return json_error(400, '你们已是好友');
        }
        // 已有待处理申请则不重复
        $exists = FriendRequest::where('from_id', $uid)
            ->where('to_id', $toId)
            ->where('status', 0)->find();
        if ($exists) {
            return json_error(400, '已发送过申请，请等待处理');
        }

        FriendRequest::create([
            'from_id' => $uid,
            'to_id'   => $toId,
            'message' => $message,
            'status'  => 0,
        ]);
        return json_ok(null, '申请已发送');
    }

    /**
     * 我收到的申请列表
     */
    public function requests(Request $request)
    {
        $uid = (int) $request->uid;
        $rows = Db::name('friend_request')
            ->alias('r')
            ->join('user u', 'u.id = r.from_id')
            ->where('r.to_id', $uid)
            ->where('r.status', 0)
            ->field('r.id, r.message, r.status, r.create_time, u.id as from_uid, u.phone, u.nickname, u.avatar')
            ->order('r.create_time', 'desc')
            ->select();
        return json_ok($rows->toArray());
    }

    /**
     * 处理申请 status: 1同意 2拒绝
     */
    public function handle(Request $request)
    {
        $uid    = (int) $request->uid;
        $reqId  = (int) $request->param('id');
        $status = (int) $request->param('status');

        if (!in_array($status, [1, 2], true)) {
            return json_error(400, '参数错误');
        }

        $req = FriendRequest::where('id', $reqId)->where('to_id', $uid)->find();
        if (!$req) {
            return json_error(404, '申请不存在');
        }
        if ($req->status !== 0) {
            return json_error(400, '该申请已处理');
        }

        Db::startTrans();
        try {
            $req->status = $status;
            $req->save();

            if ($status === 1) {
                $fromId = (int) $req->from_id;
                FriendModel::create(['user_id' => $uid, 'friend_id' => $fromId]);
                FriendModel::create(['user_id' => $fromId, 'friend_id' => $uid]);
            }
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            \think\facade\Log::error('friend handle error: ' . $e->getMessage());
            return json_error(500, '处理失败');
        }

        return json_ok(null, $status === 1 ? '已同意' : '已拒绝');
    }

    /**
     * 删除好友
     */
    public function delete(Request $request)
    {
        $uid   = (int) $request->uid;
        $fid   = (int) $request->param('id');

        FriendModel::where('user_id', $uid)->where('friend_id', $fid)->delete();
        FriendModel::where('user_id', $fid)->where('friend_id', $uid)->delete();
        return json_ok(null, '已删除');
    }
}