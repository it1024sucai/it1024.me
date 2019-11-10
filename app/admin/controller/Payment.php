<?php

namespace app\admin\controller;


use app\model\Payments;
use think\facade\View;

class Payment extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $list = Payments::select();

        View::assign('list', $list);
        return View::fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        if (request()->isAjax()) {
            $this->save();
        }
        return View::fetch('info');
    }

    /**
     * 保存新建的资源
     *
     * @return \think\Response
     */
    public function save()
    {
        //
        $data           = input('post.');
        $data['status'] = $data['status'] ? 1 : 0;

        if ($data) {
            if ($data['id']){
                $res = Payments::update($data);
            } else{
                unset($data['id']);
                $data['create_time'] = time();
                $res = Payments::insert($data);
            }


            $res ? $this->success('操作成功~') : $this->error('操作失败~');
        }
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
        if (request()->isAjax()) {
            $this->save();
        }
        $info = Payments::find(input('id'))->toArray();
        View::assign($info);
        return View::fetch('info');
    }
}
