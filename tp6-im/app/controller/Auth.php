<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\User;
use app\service\JwtService;
use think\Request;

class Auth extends BaseController
{
    /**
     * 注册
     */
    public function register(Request $request)
    {
        $phone    = trim((string) $request->param('phone'));
        $password = (string) $request->param('password');
        $nickname = trim((string) $request->param('nickname', ''));

        if (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
            return json_error(400, '手机号格式不正确');
        }
        if (strlen($password) < 6 || strlen($password) > 32) {
            return json_error(400, '密码长度需为 6-32 位');
        }
        if ($nickname === '') {
            $nickname = '用户' . substr($phone, -4);
        }

        if (User::where('phone', $phone)->find()) {
            return json_error(400, '该手机号已注册');
        }

        $user = User::create([
            'phone'    => $phone,
            'password' => $password,
            'nickname' => $nickname,
        ]);

        $token = (new JwtService())->issue((int) $user->id);
        return json_ok(['token' => $token, 'user' => $user], '注册成功');
    }

    /**
     * 登录
     */
    public function login(Request $request)
    {
        $phone    = trim((string) $request->param('phone'));
        $password = (string) $request->param('password');

        $user = User::where('phone', $phone)->find();
        if (!$user || !password_verify($password, $user->password)) {
            return json_error(400, '手机号或密码错误');
        }

        $token = (new JwtService())->issue((int) $user->id);
        return json_ok(['token' => $token, 'user' => $user], '登录成功');
    }

    /**
     * 登出（客户端丢弃 token 即可，此处占位）
     */
    public function logout()
    {
        return json_ok(null, '已退出');
    }
}