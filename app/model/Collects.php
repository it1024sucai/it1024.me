<?php

namespace app\model;

use think\Model;

class Collects extends Model
{
    //

    /**
     * 获取我的收藏
     * @param $where
     * @param int $page
     * @return array
     */
    public static function getCollect($where, $page = 1)
    {
        $collect = Collects::alias('a')->field('a.title,a.id,a.vid,b.path thumb,a.create_time,a.channel')
            ->join('attachments b', 'a.thumb=b.id')->where($where)->order('id desc')
            ->paginate(12, false, ['page' => $page]);

        return $collect ? $collect->toArray() : [];
    }
}
