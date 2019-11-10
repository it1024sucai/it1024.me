<?php

namespace app\admin\validate;

use think\Validate;

class Users extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = ['username' => 'required', 'password' => 'required'];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = ['username.required' => '用户名不能为空~', 'password.required' => '密码不能为空~'];
}
