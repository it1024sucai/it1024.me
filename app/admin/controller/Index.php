<?php

namespace app\admin\controller;


use app\model\Users;
use think\facade\View;

class Index extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        return View::fetch();
    }

    //个人设置
    public function profile() {
        if (request()->isAjax()) {
            $data                = input('post.');

            if($data['password']){
                $data['code']        = mt_rand(10000,99999);
                $data['password']    = md5($data['code'] . $data['password']);
            }

            $data['status']      = $data['status'] == 'on' ? 1 : 0;
            $data['create_time'] = time();
            $data['update_time'] = time();
            unset($data['ajax']);

            $res                 = Users::update($data);
            if ($res) {
                $this->success('编辑成功');
            } else {
                $this->error('编辑失败');
            }
        }
        $info = Users::find(session('admin.id'));
        View::assign('info', $info);
        return View::fetch();
    }

}
