<?php
/**
 * Created by PhpStorm.
 * User: 659998662
 * Date: 2019/1/20
 * Time: 21:58
 */

namespace app\model;


use think\Model;

class FileEntries extends Model {

    /**
     * 递归遍历获取所有子节点id
     * @param int $pid 父级id
     * @author 胡卫兵 <659998662@qq.com>
     * @return array
     */
    public static function getChildsId($pid = 0)
    {
        $ids = self::where('parent_id', $pid)->column('id');
        foreach ($ids as $value) {
            $ids = array_merge($ids, self::getChildsId($value));
        }
        return $ids;
    }


    /**
     * 复制数据
     * @param $id
     * @param $user_id
     * @param $parent_id
     * @return bool
     */
    public static function copy($id, $user_id, $parent_id)
    {
        try {
            $list = self::field('id,name,type,file_type,mime,size,path,thumb,ext,md5,share')->where('parent_id', $parent_id)->select();
            if ($list) {
                $list = $list->toArray();
                foreach ($list as $item) {
                    if ($item['id']) {
                        $time                = time();
                        $parent_id           = $item['id'];
                        $item['parent_id']   = $id;
                        $item['user_id']     = $user_id;
                        $item['status']      = 1;
                        $item['create_time'] = $time;
                        $item['update_time'] = $time;

                        unset($item['id']);
                        $insertId = self::insertGetId($item);

                        if ($item['type'] === 'dir')
                            self::copy($insertId, $user_id, $parent_id);

                        unset($insertId);
                        unset($item);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }


    /**
     * 递归遍历获取所有文件夹子节点id
     * @param int $pid 父级id
     * @author 胡卫兵 <659998662@qq.com>
     * @return array
     */
    public static function getDirsId($pid = 0)
    {
        $ids = self::where([['parent_id', '=', $pid]])->column('id');
        foreach ($ids as $value) {
            $ids = array_merge($ids, self::getDiresId($value));
        }
        return $ids;
    }


    /**
     * 获取所有两级文件夹节点
     * @param int $pid 父级id
     * @author 胡卫兵 <659998662@qq.com>
     * @return array
     */
    public static function getDirChild($where, $ids)
    {
        $list = self::field('id,parent_id,name,type')->where($where)->whereNotIn('id', $ids)->select();
        foreach ($list as $key => $value) {
            $where['parent_id'] = $value['id'];
            $data               = $value;
            $data['data']       = self::field('id,parent_id,name,type')->where($where)->whereNotIn('id', $ids)->select();
            $list[$key]         = $data;
            unset($data);
        }
        return $list;
    }

}