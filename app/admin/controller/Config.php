<?php

namespace app\admin\controller;

use app\model\Configs;
use think\facade\View;

class Config extends Base
{
    /**
     * 列表页
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(){
        $field = \input('_order','sort');
        $_by  = \input('_by','asc');
        // 查询
        $map   = $this->getMap();
        if($map)
            $list = Configs::where($map)->order($field,$_by)->paginate(20, false, ['query' => input()]);
        else
            $list = Configs::order($field,$_by)->paginate(20,false, ['query'=>input('get.')]);

        return View::fetch('',['list' => $list,'field'=>$field]);
    }

    /**
     * 新增页面
     *
     * @return array|mixed
     */
    public function create(){
        if(request()->isAjax()){
            return $this->save('configs');
        }
        return View::fetch('info');
    }

    /**
     * 编辑页面
     * @return array|mixed
     */
    public function edit(){
        if(request()->isAjax()){
            return $this->save('configs');
        }

        $info = Configs::where('id',input('id'))->find()->toArray();

        View::assign($info);
        return View::fetch('info');
    }

}
