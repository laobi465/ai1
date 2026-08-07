<?php
declare(strict_types=1);

namespace app\service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JWT 服务
 */
class JwtService
{
    /** 签发密钥 */
    protected string $secret;

    /** 有效期(秒) */
    protected int $ttl;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', 'im_default_secret_change_me_2026');
        $this->ttl    = (int) env('JWT_TTL', 7 * 24 * 3600);
    }

    /**
     * 签发 token
     */
    public function issue(int $uid): string
    {
        $payload = [
            'iss' => 'im-app',
            'iat' => time(),
            'exp' => time() + $this->ttl,
            'uid' => $uid,
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * 解析 token，返回 uid；失败返回 null
     */
    public function parse(string $token): ?int
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return isset($decoded->uid) ? (int) $decoded->uid : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}