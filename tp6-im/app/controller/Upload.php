<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use think\Request;
use think\facade\Filesystem;

class Upload extends BaseController
{
    /** 允许的图片类型 */
    private const IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /** 允许的语音类型 */
    private const VOICE_TYPES = ['mp3', 'wav', 'amr', 'm4a'];

    /**
     * 上传文件 type: image|voice
     */
    public function index(Request $request)
    {
        $type = (string) $request->param('type', 'image');
        $file = $request->file('file');

        if (!$file) {
            return json_error(400, '未上传文件');
        }

        $allow = $type === 'voice' ? self::VOICE_TYPES : self::IMAGE_TYPES;
        $ext   = strtolower($file->getOriginalExtension());

        if (!in_array($ext, $allow, true)) {
            return json_error(400, '不支持的文件类型');
        }

        try {
            $saveName = Filesystem::disk('public')->putFile($type === 'voice' ? 'voice' : 'image', $file);
            $url      = '/storage/' . str_replace('\\', '/', $saveName);
        } catch (\Throwable $e) {
            return json_error(500, '上传失败：' . $e->getMessage());
        }

        return json_ok(['url' => $url], '上传成功');
    }
}