<?php
declare(strict_types=1);

namespace app\middleware;

use Closure;
use think\Request;
use think\Response;

/**
 * 跨域中间件：允许 H5/小程序等前端跨域访问 API
 */
class CrossDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = [
            'Access-Control-Allow-Origin'      => $request->header('Origin', '*'),
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, X-Requested-With',
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Max-Age'           => '86400',
        ];

        // 预检请求直接返回
        if ($request->method(true) === 'OPTIONS') {
            return Response::create()->code(204)->header($header);
        }

        return $next($request)->header($header);
    }
}