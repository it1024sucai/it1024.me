<?php

namespace app\model;

use think\Model;

class Categorys extends Model
{
    //
    protected $pk = 'id';

    /**
     * 递归获取父ID下的所有下级分类信息
     * @param $id
     * @return array
     */
    public static function getLists($id = 0)
    {
        $where = ['parent_id' => $id];
        $res   = self::field('id,name,parent_id,status')->where($where)->order('sort asc')->select()->toArray();
        if ($res) {
            foreach ($res as $v) {
                $res = array_merge(self::getLists($v['id']), $res);
            }
        }
        return $res;
    }

    /**
     * 递归获取指定id下的所有id
     * @param $id
     * @return array
     */
    public static function getIds($id)
    {
        $where = ['parent_id' => $id];
        $res   = self::where($where)->column('id');
        if ($res) {
            foreach ($res as $v) {
                $res = array_merge(self::getIds($v['id']), $res);
            }
        }
        return $res;
    }
}
