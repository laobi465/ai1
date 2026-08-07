<?php
// 应用公共文件
use think\facade\Log;

if (!function_exists('json_return')) {
    /**
     * 统一 JSON 响应
     * @param int    $code 业务码 0 成功
     * @param string $msg  提示信息
     * @param mixed  $data 返回数据
     */
    function json_return(int $code, string $msg = '', $data = null)
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }
}

if (!function_exists('json_ok')) {
    function json_ok($data = null, string $msg = 'ok')
    {
        return json_return(0, $msg, $data);
    }
}

if (!function_exists('json_error')) {
    function json_error(int $code, string $msg)
    {
        return json_return($code, $msg);
    }
}