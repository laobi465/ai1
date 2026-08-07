<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Conversation extends Model
{
    protected $name = 'conversation';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /** 消息类型常量 */
    public const TYPE_SINGLE = 1;
    public const TYPE_GROUP  = 2;

    public function members()
    {
        return $this->hasMany(ConversationMember::class, 'conversation_id', 'id');
    }
}