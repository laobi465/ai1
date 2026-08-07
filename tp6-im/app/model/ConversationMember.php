<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class ConversationMember extends Model
{
    protected $name = 'conversation_member';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}