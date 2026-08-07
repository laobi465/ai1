<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Message extends Model
{
    protected $name = 'message';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public const TYPE_TEXT  = 1;
    public const TYPE_IMAGE = 2;
    public const TYPE_VOICE = 3;
    public const TYPE_SYSTEM = 4;
}