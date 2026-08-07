<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Friend extends Model
{
    protected $name = 'user_friend';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = false;
}