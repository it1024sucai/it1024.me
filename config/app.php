<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

use think\facade\Env;

return [
    // 应用地址
    'app_host'         => Env::get('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 应用Trace
    'app_trace'        => false,
    // 是否启用路由
    'with_route'       => true,
    // 是否启用事件
    'with_event'       => true,
    // 自动多应用模式
    'auto_multi_app'   => true,
    // 默认应用
    'default_app'      => 'index',

    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind' => [
        'admin'        =>  'admin',  //  admin子域名绑定到admin应用
        '*'            =>  'index', // 二级泛域名绑定到home应用
    ],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => false,

    'upload_file_size'=>0,
    'upload_file_ext'=>'zip,rar,gif,jpg,jpeg,bmp,png',
    'upload_image_size'=>0,
    'upload_image_ext'=>'gif,jpg,jpeg,bmp,png',
    'upload_path'=>'uploads',
    'upload_image_thumb'=>'',
    'upload_image_thumb_type'=>3,
    'upload_thumb_water'=>1,
    'upload_thumb_water_pic'=>'./shuiyin.png',
    'upload_thumb_water_position'=>9,
    'upload_thumb_water_alpha'=>100,
    'upload_driver'=>'local',
];
