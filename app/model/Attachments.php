<?php

namespace app\model;

use think\Model;

/**
 * 附件模型
 * @package app\admin\model
 */
class Attachments extends Model
{
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 根据附件id获取路径
     * @param  string|array $id 附件id
     * @return string|array     路径
     */
    public function getFilePath($id = '')
    {
        $result = $this->where('id', $id)->field('path,driver')->cache(60)->find();
        if ($result) {
            if ($result['driver'] == 'local') {
                return $result['path'];
            } else {
                return $result['path'];
            }
        } else {
            return false;
        }
    }

    /**
     * 根据图片id获取缩略图路径，如果缩略图不存在，则返回原图路径
     * @param string $id 图片id* @returnmixed
     */
    public function getThumbPath($id = '')
    {
        $result = $this->where('id', $id)->field('path,driver,thumb')->cache(60)->find();
        if ($result) {
            if ($result['driver'] == 'local') {
                return $result['thumb'] != '' ? $result['thumb'] : $result['path'];
            } else {
                return $result['thumb'] != '' ? $result['thumb'] : $result['path'];
            }
        } else {
            return false;
        }
    }

    /**
     * 根据附件id获取名称
     * @param  string $id 附件id
     * @return string     名称
     */
    public function getFileName($id = '')
    {
        return $this->where('id', $id)->value('name');
    }
}
