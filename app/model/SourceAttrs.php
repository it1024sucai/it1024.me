<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class SourceAttrs extends Model
{
    //
    public static function getAll()
    {
        $list = self::where('status', 1)->order('sort')->cache(360)->select()->toArray();

        return $list;
    }

    public static function getByName($channel, $name)
    {
        $data = [];
        $list = self::getAll();
        foreach ($list as $k => $v) {
            if ($v['channel'] == $channel && $v['name'] == $name) {
                $data[] = $v;
            }
        }

        return $data;
    }

    public static function getById($id)
    {
        $data = [];
        $list = self::getAll();
        foreach ($list as $k => $v) {
            if ($v['id'] == $id) {
                $data = $v;
                break;
            }
        }

        return $data;
    }
}
