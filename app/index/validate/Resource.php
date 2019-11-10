<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Resource extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
	    '__token__'=>'require|max:25|token',
	    'title'=>'require|max:100',
	    'type'=>'require|number',
	    //'industry'=>'require|number',
	    'layout'=>'require|number',
	    'color'=>'require|number',
	    'system'=>'require|number',
	    'thumb'=>'require|number',
	    'file_id'=>'require|number',
	    'language'=>'require',
	    'ie'=>'number',
	    'ints'=>'number',
	    'description'=>'require',
	    'tags'=>'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [];

    protected $scene = [
        'jquery'        =>  ['title','type','thumb','file_id','ie','ints','description'],
        'source'        =>  ['title','type','industry','layout','system','thumb','file_id','ie','ints','description'],
        'templates'     =>  ['title','type','layout', 'color', 'language','thumb','file_id','ie','ints','description'],
    ];
}
