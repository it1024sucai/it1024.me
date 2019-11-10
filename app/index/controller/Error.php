<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/11/4
 * Time: 11:09
 */

namespace app\index\controller;


use app\BaseController;

class Error extends BaseController
{
    public function __call($method, $args)
    {
        $this->error('404');
    }
}