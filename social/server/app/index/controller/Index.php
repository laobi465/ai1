<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Cache;

class Index extends BaseController
{
    public function index()
    {
        $dbStatus = 'ok';
        try {
            $rows = Db::name('test')->select();
            $dbStatus = 'ok (' . count($rows) . ' rows)';
        } catch (\Throwable $e) {
            $dbStatus = 'error: ' . $e->getMessage();
        }

        $redisStatus = 'ok';
        try {
            Cache::store('redis')->set('test_key', 'redis-ok', 60);
            $redisStatus = Cache::store('redis')->get('test_key');
        } catch (\Throwable $e) {
            $redisStatus = 'error: ' . $e->getMessage();
        }

        return json([
            'code' => 0,
            'msg'  => '社交系统 API',
            'data' => [
                'name' => 'Social System',
                'version' => '1.0.0',
                'db'   => $dbStatus,
                'redis' => $redisStatus,
            ]
        ]);
    }
}