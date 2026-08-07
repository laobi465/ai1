<?php
declare(strict_types=1);

/**
 * IM 实时推送服务（Workerman WebSocket）
 * 启动：php worker/server.php start
 * 复用 ThinkPHP 应用层（MessageService / Db）做消息落库与路由。
 */

use app\model\Message;
use app\service\JwtService;
use app\service\MessageService;
use think\App;
use think\facade\Db;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

require __DIR__ . '/../vendor/autoload.php';

// 初始化 ThinkPHP 应用（加载 env/config/db）
$app = new App();
$app->initialize();

/**
 * uid => TcpConnection 在线映射
 * @var array<int, TcpConnection>
 */
$connections = [];

function send_json(TcpConnection $conn, array $data): void
{
    $conn->send(json_encode($data, JSON_UNESCAPED_UNICODE));
}

$ws = new Worker('websocket://0.0.0.0:8282');
$ws->count = 4;
$ws->name = 'IM-WS';

$ws->onMessage = function (TcpConnection $conn, $data) use (&$connections) {
    $frame = json_decode($data, true);
    if (!is_array($frame) || !isset($frame['type'])) {
        return;
    }

    switch ($frame['type']) {
        // ---- 鉴权 ----
        case 'auth':
            $uid = (new JwtService())->parse((string) ($frame['token'] ?? ''));
            if ($uid === null) {
                send_json($conn, ['type' => 'auth_fail', 'msg' => '鉴权失败']);
                $conn->close();
                return;
            }
            $conn->uid = $uid;
            $connections[$uid] = $conn;
            send_json($conn, ['type' => 'auth_ok', 'uid' => $uid]);
            break;

        // ---- 发送消息 ----
        case 'send':
            $uid = $conn->uid ?? null;
            if ($uid === null) {
                send_json($conn, ['type' => 'error', 'msg' => '未鉴权']);
                break;
            }
            $convId      = (int) ($frame['conv_id'] ?? 0);
            $msgType     = (int) ($frame['msg_type'] ?? Message::TYPE_TEXT);
            $content     = (string) ($frame['content'] ?? '');
            $duration    = (int) ($frame['duration'] ?? 0);
            $clientMsgId = (string) ($frame['client_msg_id'] ?? '');

            if (!in_array($msgType, [Message::TYPE_TEXT, Message::TYPE_IMAGE, Message::TYPE_VOICE], true)) {
                send_json($conn, ['type' => 'error', 'msg' => '不支持的消息类型']);
                break;
            }
            if (!Db::name('conversation_member')->where('conversation_id', $convId)->where('user_id', $uid)->find()) {
                send_json($conn, ['type' => 'error', 'msg' => '不在会话中']);
                break;
            }

            $msgId = (new MessageService())->create($convId, $uid, $msgType, $content, $duration, $clientMsgId);
            $row   = Db::name('message')->where('id', $msgId)->find();

            // 广播给会话内所有在线成员
            $members = Db::name('conversation_member')->where('conversation_id', $convId)->column('user_id');
            foreach ($members as $muid) {
                if (isset($connections[$muid])) {
                    send_json($connections[$muid], ['type' => 'message', 'data' => $row]);
                }
            }
            break;

        // ---- 已读回执 ----
        case 'read':
            $uid = $conn->uid ?? null;
            if ($uid === null) {
                break;
            }
            $convId    = (int) ($frame['conv_id'] ?? 0);
            $lastMsgId = (int) ($frame['last_msg_id'] ?? 0);

            Db::name('conversation_member')
                ->where('conversation_id', $convId)->where('user_id', $uid)
                ->update(['unread_count' => 0, 'last_read_message_id' => $lastMsgId]);

            $members = Db::name('conversation_member')->where('conversation_id', $convId)->column('user_id');
            foreach ($members as $muid) {
                if ($muid !== $uid && isset($connections[$muid])) {
                    send_json($connections[$muid], [
                        'type' => 'read_ack', 'conv_id' => $convId,
                        'last_msg_id' => $lastMsgId, 'by_uid' => $uid,
                    ]);
                }
            }
            break;

        // ---- 心跳 ----
        case 'ping':
            send_json($conn, ['type' => 'pong']);
            break;
    }
};

$ws->onClose = function (TcpConnection $conn) use (&$connections) {
    if (isset($conn->uid) && isset($connections[$conn->uid]) && $connections[$conn->uid] === $conn) {
        unset($connections[$conn->uid]);
    }
};

// 服务端心跳，60s 无响应则断开，避免僵尸连接
$ws->pingInterval = 50;
$ws->pingData     = '{"type":"pong"}';

Worker::runAll();