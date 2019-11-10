<?php

namespace app\admin\controller;


use app\BaseController;
use app\model\Users;
use think\facade\Session;
use think\facade\View;

class Login extends BaseController
{
    /**
     * 登录操作
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (request()->isAjax()) {
            $data = input('post.');
            $validate = new \app\admin\validate\Users;
            $result   = $validate->check($data);
            if ($result) {
                $msg = $validate->getError();
                return json(['code' => 0, 'msg' => $msg]);
            }


            $msg = Users::adminLogin($data);
            return json($msg);
        }
        return View::fetch();
    }

    /**
     * 退出登录
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Session::delete('admin');
        header("Location:" . url('/login/index'));
    }

    function test()
    {
        send_email('659998662@qq.com', 'bing', 'c测试', 'sdfdssdgdsgadsg');
    }

}
