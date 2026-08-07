<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\User as UserModel;
use think\Request;

class User extends BaseController
{
    /**
     * 当前用户资料
     */
    public function me(Request $request)
    {
        $user = UserModel::find($request->uid);
        if (!$user) {
            return json_error(404, '用户不存在');
        }
        return json_ok($user);
    }

    /**
     * 修改资料（昵称/签名/头像）
     */
    public function update(Request $request)
    {
        $user = UserModel::find($request->uid);
        if (!$user) {
            return json_error(404, '用户不存在');
        }

        $data = [];
        foreach (['nickname', 'signature', 'avatar'] as $field) {
            if ($request->has($field)) {
                $val = trim((string) $request->param($field));
                if ($val !== '') {
                    $data[$field] = $val;
                }
            }
        }
        if (!empty($data)) {
            $user->save($data);
        }
        return json_ok($user->refresh(), '已更新');
    }

    /**
     * 搜索用户（按手机号或昵称）
     */
    public function search(Request $request)
    {
        $kw = trim((string) $request->param('kw'));
        if ($kw === '') {
            return json_ok([]);
        }

        $query = UserModel::where('id', '<>', $request->uid);
        if (preg_match('/^1[3-9]\d{9}$/', $kw)) {
            $query->where('phone', $kw);
        } else {
            $query->where('nickname', 'like', "%{$kw}%");
        }

        $list = $query->field('id,phone,nickname,avatar,signature')->limit(20)->select();
        return json_ok($list);
    }
}