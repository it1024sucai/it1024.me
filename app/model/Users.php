<?php

namespace app\model;


use think\Model;


class Users extends Model
{
    protected $pk         = 'id';
    public    $timestamps = false;

    //后台登录
    static function adminLogin($data)
    {
        $user = self::field('id,username,nickname,password,code,role_id')->where('username', $data['username'])->find();
        if ($user) {
            if ($user['password'] == md5($user['code'] . $data['password'])) {

                $role_menu_auth = Roles::where('id', $user['role_id'])->value('nodes');
                $role_menu_auth = explode(',', $role_menu_auth);
                session('role_menu_auth', $role_menu_auth);
                $user->login_time = time();
                $user->login_ip   = get_ip();
                $user->save();

                $user = $user->toArray();
                unset($user['password']);
                unset($user['code']);
                session('admin', $user);
                return ['code' => 1, 'msg' => '登录成功~', 'url' => url('/')];
            }
            return ['code' => 0, 'msg' => '用户密码错误~'];
        }
        return ['code' => 0, 'msg' => '用户名或密码错误~'];
    }


    /**
     * 递归获取父ID下的所有下级分类信息
     * @param $id
     * @return array
     */
    public static function getLists($id = 0)
    {
        $where = ['parent_id' => $id];
        $res   = self::field('id,name,parent_id,status')->where($where)->order('sort asc')->cache(300)->select();
        if ($res) {
            $res = $res->toArray();
            foreach ($res as $v) {
                $res = array_merge(self::getLists($v['id']), $res);
            }
        }
        return $res;
    }

    /**
     * 获取下级人数
     * @param $id
     * @return array
     */
    public static function getCounts($id)
    {
        $where  = ['parent_id' => $id, 'is_recharge' => 1];
        $counts = self::where($where)->cache(300)->count('id');
        return $counts;
    }

    /**
     * 获取所有父节点id
     * @param int $id 节点id
     * @author 胡卫兵 <659998662@qq.com>
     * @return array
     */
    public static function getParentsId($id, $level = 1)
    {
        $pid = self::where('id', $id)->value('parent_id');
        if ($pid != 0 && $level < 6) {
            $pids[$level] = $pid;
            $level++;
            self::getParentsId($pid, $level);
        }
        return $pids;
    }

    /**
     * 设置推广码
     * @return string
     */
    public static function getCode($id)
    {
        $code = self::where('id', $id)->cache(600)->value('my_code');
        if (!$code) {
            $code = getAssignStr(6);
            if (self::where('my_code', $code)->value('id')) {
                self::getCode($id);
            }
            Users::update(['id' => $id, 'my_code' => $code]);
        }
        return $code;
    }

    /**
     * 获取用户信息
     * @param $id
     * @return array
     */
    public static function getInfo($id, $field = '*')
    {
        $res = Users::field($field)->cache(300)->find($id);
        if ($res) {
            return $res->toArray();
        }
        return [];
    }

    public static function getBaseInfo($id)
    {
        $res = Users::field('id,username,nickname,job,avatar,default_avatar,exp,coin,points,area,remark')->cache(300)
            ->find($id);
        if ($res) {
            $info           = $res->toArray();
            $info['avatar'] = get_avatar($info['avatar'], $info['default_avatar']);
            $level          = UsersLevels::getLevel($info['exp']);
            $info           = array_merge($info, $level);
            return $info;
        }
        return [];
    }
}
