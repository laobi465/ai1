<?php
// +----------------------------------------------------------------------
// | IM 即时通讯 API 路由
// +----------------------------------------------------------------------
use think\facade\Route;

// 公开接口（无需登录）
Route::post('api/auth/register', 'Auth/register');
Route::post('api/auth/login', 'Auth/login');

// 需要登录的接口分组
Route::group('api', function () {
    // 认证
    Route::post('auth/logout', 'Auth/logout');

    // 用户
    Route::get('user/me', 'User/me');
    Route::put('user/me', 'User/update');
    Route::get('user/search', 'User/search');

    // 好友
    Route::get('friend/list', 'Friend/list');
    Route::post('friend/request', 'Friend/request');
    Route::get('friend/requests', 'Friend/requests');
    Route::post('friend/handle', 'Friend/handle');
    Route::delete('friend/:id', 'Friend/delete');

    // 会话
    Route::get('conversation/list', 'Conversation/list');
    Route::post('conversation/single', 'Conversation/createSingle');
    Route::post('conversation/:id/read', 'Conversation/read');

    // 群
    Route::post('group', 'GroupController/create');
    Route::get('group/:id', 'GroupController/detail');
    Route::post('group/:id/invite', 'GroupController/invite');
    Route::delete('group/:id/member/:mid', 'GroupController/remove');
    Route::put('group/:id', 'GroupController/rename');
    Route::delete('group/:id', 'GroupController/dissolve');

    // 消息
    Route::get('message/history', 'MessageController/history');
    Route::post('message/send', 'MessageController/send');

    // 上传
    Route::post('upload', 'Upload/index');
})->middleware(\app\middleware\Auth::class);