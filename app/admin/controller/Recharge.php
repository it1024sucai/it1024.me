<?php

namespace app\admin\controller;

use app\model\RechargeLogs;
use think\facade\View;

class Recharge extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $where = $this->getMap();
        $where[] = ['a.create_time', '>', 0];
        $list = RechargeLogs::alias('a')->field('a.*,b.username')->join('users b','a.uid=b.id')->where($where)->order('create_time desc')->paginate(20,false, ['query'=>input('get.')]);

        View::assign('list',$list);

        return View::fetch();
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

}
