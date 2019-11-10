<?php

namespace app\model;

use think\Model;

class RebateLogs extends Model
{
    //

    public static function getList($uid = 0, $page = 1, $row = 18)
    {
        if ($uid) {
            $list = self::where(['uid' => $uid])->paginate($row, false, ['page' => $page]);
        } else {
            $list = self::paginate($row, false, ['page' => $page]);
        }

        if ($list) {
            $list->toArray();
        } else {
            $list = [];
        }

        return $list;
    }
}
