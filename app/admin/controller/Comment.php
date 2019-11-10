<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/4/21
 * Time: 23:29
 */

namespace app\admin\controller;

use app\model\Categorys;
use app\model\Comments;
use think\facade\View;

class Comment extends Base {

    public function index(){
        $type = input('type/s');
        $status = input('status');
        $title = input('title');

        $type == true && $where[] = ['b.channel','=',$type];
        $title == true && $where[] = ['b.title|a.nickname','like',"%$title%"];
        is_numeric($status) == true && $where[] = ['a.status','=',$status];

        $category= Categorys::where('parent_id=0')->select();
        $comments = Comments::alias('a')->field('a.*,b.title,b.channel')->join('videos b','a.vid=b.id')->where($where)->order('id desc')->paginate('',false,['query'=>input()]);

        if(is_numeric($status) == false) $status = '0,1,-1';

        $statusArr = ['1'=>'<span class="label label-success">通过</span>','0'=>'<span class="label label-default">未审核</span>','-1'=>'<span class="label label-danger">不通过</span>'];
        View::assign('statusArr', $statusArr);
        View::assign('comments',$comments);
        View::assign('type',$type);
        View::assign('status',$status);
        View::assign('category',$category);
        return View::fetch();
    }

    public function info(){

        $info = Comments::alias('a')->field('a.*,b.title')->join('videos b','a.vid=b.id')->find(input('id/d'));
        View::assign('info',$info);
        return View::fetch();
    }


    public function reply(){
        $nickname = input('nickname/s');
        $content = input('content/s');
        $vid = input('vid/d');
        $id = input('id/d');

        if(!$content){
            return ['code'=>0,'msg'=>'请输入回复内容'];
        }

        $data = [
            'uid'=>1,
            'parent_id'=>$id,
            'vid'=>$vid,
            'nickname'=>$nickname,
            'content'=>$content,
        ];

        $res = Comments::insertGetId($data);

        if($res){
            return ['code'=>1,'msg'=>'回复成功'];
        }else{
            return ['code'=>0,'msg'=>'回复失败'];
        }

    }
}