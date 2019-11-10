<?php
declare (strict_types=1);

namespace app\index\controller;

use app\model\Collects;
use app\model\Resources;
use app\model\Users;
use think\App;
use think\facade\View;

class User extends Common
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $action = \request()->action();
        $id     = input('id/d');
        if (!$id) {
            $this->error(404);
        }
        $user            = Users::getBaseInfo($id);
        $data['user']    = $user;
        $data['user_id'] = $id;
        $data['action']  = $action;
        $seo             = [
            'seo_title' => $user['nickname'] . '的主页-' . config('app.web_site_title'),
            'keywords'  => config('app.web_site_keywords'), 'description' => config('app.web_site_description')
        ];
        View::assign($seo);
        View::assign($data);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $id    = input('id/d');
        $where = [
            ['user_id', '=', $id], ['a.status', '=', 1],
        ];

        $list = Resources::alias('a')->field('a.id,a.channel,a.title,a.description,b.thumb')
            ->leftJoin('attachments b', 'a.thumb=b.id')->where($where)->select()->toArray();
        View::assign('list', $list);
        return View::fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function forum()
    {
        $id    = input('id/d');
        $where = [
            ['user_id', '=', $id], ['a.status', '=', 1],
        ];

        $list = Resources::alias('a')->field('a.id,a.channel,a.title,a.description,b.thumb')
            ->leftJoin('attachments b', 'a.thumb=b.id')->where($where)->select()->toArray();
        View::assign('list', $list);
        return View::fetch('index');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function collect()
    {
        $id    = input('id/d');
        $where = [
            ['a.user_id', '=', $id], ['a.status', '=', 1],
        ];

        $list = Collects::alias('a')->field('a.id,a.channel,a.title,c.description,b.thumb')
            ->leftJoin('attachments b', 'a.thumb=b.id')->leftJoin('resources c', 'a.res_id=c.id')->where($where)->select()->toArray();
        View::assign('list', $list);
        return View::fetch('index');
    }
}
