<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Group extends Model
{
    protected $name = 'group';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function members()
    {
        return $this->hasMany(GroupMember::class, 'group_id', 'id');
    }
}