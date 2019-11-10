<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/11/1
 * Time: 23:15
 */

namespace app\admin\controller;


use app\model\UsersLevels;
use think\facade\View;

class Level extends Base
{
    function index()
    {
        $list = UsersLevels::field('*')->select();

        View::assign('list', $list);
        return View::fetch();
    }


    public function edit()
    {
        if (request()->isAjax()) {
            $this->save();
        }

        $info = UsersLevels::find(input('id'))->toArray();
        View::assign($info);
        return View::fetch('info');
    }


    public function create()
    {

        if (request()->isAjax()) {
            $this->save();
        }
        return View::fetch('info');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        $data = input('post.');
        if ($data['id']) {
            $res = UsersLevels::update($data);
        } else {
            $res = UsersLevels::insert($data);
        }

        $res ? $this->success('操作成功~') : $this->error('操作失败~');

    }
}