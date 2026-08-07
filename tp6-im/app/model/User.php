<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class User extends Model
{
    protected $name = 'user';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /** 隐藏敏感字段 */
    protected $hidden = ['password'];

    public function setPasswordAttr($value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }
}