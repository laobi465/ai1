<?php
declare(strict_types=1);

namespace app\middleware;

use app\service\JwtService;
use Closure;
use think\Request;
use think\Response;

/**
 * Token 鉴权中间件
 * 校验 Authorization: Bearer <token>，通过后将 uid 写入 Request
 */
class Auth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractToken($request);
        if (!$token) {
            return json_error(401, '未登录或缺少凭证');
        }

        $uid = (new JwtService())->parse($token);
        if (!$uid) {
            return json_error(401, '登录已过期，请重新登录');
        }

        $request->uid = $uid;
        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        if (preg_match('/Bearer\s+(.+)/i', $header, $m)) {
            return $m[1];
        }
        return null;
    }
}