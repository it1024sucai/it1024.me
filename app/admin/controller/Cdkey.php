<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/4/27
 * Time: 16:03
 */

namespace app\admin\controller;



use think\facade\View;

class Cdkey extends Base {
    public function index() {

        return View::fetch();
    }

    //添加文章栏目
    function create() {

        return View::fetch('info');
    }

    //编辑文章栏目
    function edit() {

        return View::fetch('info');
    }
}