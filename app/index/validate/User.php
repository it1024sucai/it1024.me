<?php

namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule
        = [
            'username'=>'require|alphaNum|length:6,10',
            'nickname' => 'require|chsAlpha|length:2,8',
            'password' => 'require|alphaNum|length:6,18',
            'repassword'=>'require|confirm:password'
        ];

    protected $message
        = [
            'username.require' => '账号不能为空',
            'username.alphaNum' => '账号只能包含数字和字母',
            'username.length' => '账号长度为6~18个字符',
            'nickname.require'    => '昵称不能为空',
            'nickname.chs'    => '昵称只能包含汉字和字母',
            'nickname.length' => '昵称长度为2-8字符',
            'password.require' => '密码不能为空',
            'password.alphaNum' => '密码只能包含数字和字母',
            'password.length' => '密码长度为6~18个字符',
            'repassword'=>'两次输入的密码不一致'
        ];

    protected $scene = [
        'login'  =>  ['username','password'],
        'updatePass'  =>  ['password','repassword'],
    ];
}