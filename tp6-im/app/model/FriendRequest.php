<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class FriendRequest extends Model
{
    protected $name = 'friend_request';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}