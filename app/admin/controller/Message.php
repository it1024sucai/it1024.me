<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/6/15
 * Time: 17:55
 */

namespace app\admin\controller;


use app\model\Messages;
use think\facade\View;

class Message extends Base
{
    public function index()
    {
        $list = Messages::field('id,title,content,create_time,status,type')->paginate(20,false, ['query'=>input('get.')]);

        View::assign('list', $list);
        return View::fetch();
    }


    function save(){
        $data = input('post.');
        $time = time();

        if(!$data['title']){
            $this->error('标题不能为空');
        }
        if(!$data['content']){
            $this->error('内容不能为空');
        }
        $data['update_time'] = $time;
        if($data['id']){
            $res = Messages::update($data);
        }else{
            $data['create_time'] = $time;
            $res = Messages::insert($data);
        }

        $res ? $this->success('操作成功~') : $this->error('操作失败');
    }

    public function create(){
        if(request()->isAjax()){
            $this->save();
        }
        return View::fetch('info');
    }


    public function edit(){
        if(request()->isAjax()){
            $this->save();
        }
        $info = Messages::find(input('id'))->toArray();
        View::assign($info);
        return View::fetch('info');
    }
}