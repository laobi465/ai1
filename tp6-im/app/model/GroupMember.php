<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class GroupMember extends Model
{
    protected $name = 'group_member';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = false;
}