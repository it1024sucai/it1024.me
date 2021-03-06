<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/20
 * Time: 21:44
 */

namespace app\admin\controller;

use app\model\Actors;
use app\model\Categorys;
use app\model\Tags;
use app\model\Resources;
use think\Db;
use think\facade\View;

class Image extends Base
{
    public $tags   = [];
    public $actors = [];

    function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $tags         = Db::table('tags')->where([['status', '=', 1], ['channel', 'in', 'level,girls']])->order('sort')
            ->select();
        $actors       = Db::table('actors')->where(['status' => 1])->order('sort')->select();
        $this->tags   = $tags;
        $this->actors = $actors;

        $category = Categorys::where('parent_id', 0)->select();
        View::assign('tags_list', $tags);
        View::assign('actors_list', $actors);
        View::assign('category', $category);
    }

    //文章列表页
    public function index()
    {
        $where   = $this->getMap();
        $where[] = ['channel', 'in', 'level,girls'];
        $list    = Resources::alias('a')->field('id,cat_id,title,thumb,resource,create_time,clicks,status,goods')
            ->where($where)->order('create_time desc, id desc')->paginate(20, false, ['query' => input('get.')]);

        View::assign('list', $list);
        return View::fetch();
    }

    public function createJson()
    {
        try {
            $hotList = Resources::alias('a')
                ->field('a.id,channel,a.title,a.score,a.goods,a.clicks,a.create_time,a.create_time,b.thumb')
                ->join('attachments b', 'a.thumb=b.id')->where('a.status', 1)->order('a.create_time desc')->limit(9)
                ->select()->toJson();
            file_put_contents('json/hotList.json', $hotList);
            $allList = Categorys::field('id,name,alias,parent_id,status')->order('sort asc')->select()->toArray();
            foreach ($allList as $k => $v) {
                $where       = [
                    ['a.status', '=', '1'], ['a.channel', '=', $v['alias']]
                ];
                $list        = Resources::alias('a')
                    ->field('a.id,channel,a.title,a.score,a.goods,a.clicks,a.create_time,a.create_time,b.thumb')
                    ->join('attachments b', 'a.thumb=b.id')->where($where)->order('a.create_time desc')->limit(12)
                    ->select()->toArray();
                $v['data']   = $list;
                $allList[$k] = $v;
                unset($where);
            }

            file_put_contents('json/allList.json', json_encode($allList));

        } catch (\Exception $e) {
            print_r($e->getMessage());
            $this->error('网络繁忙');
        }
        $this->success('操作成功');
    }

    //导入操作
    public function flag()
    {
        $ids  = input('post.');
        $flag = input('flag/d');
        Db::table('videos')->where('id', 'in', $ids['ids'])->setField(['flag' => $flag]);
        $this->success('操作成功');
    }

    //添加文章
    public function create()
    {
        if ((!request()->isPost())) {
            $this->info_save();
        }
        $token = $this->request->token('__token__', 'sha1');
        View::assign('token', $token);
        return View::fetch('info');
    }

    //资讯修改、添加操作
    function info_save()
    {
        $data = input('post.');
        $id   = $data['id'];

        if (!$data['cat_id']) {
            $this->error('请选择分类~');
        }
        if (!$data['title']) {
            $this->error('标题不能为空~');
        }
        if (!$data['thumb']) {
            $this->error('请上传封面图~');
        }
        if (!$data['tags']) {
            $this->error('请选择标签~');
        }
        if (!$data['actors'] && $data['cat_id'] == 1) {
            $this->error('请选择女优~');
        }

        if ($data['cat_id '] == 3 || $data['cat_id'] == 4) {
            if (!$data['images']) {
                $this->error('请上传图片内容~');
            }
        }
        $keywords = '';
        try {
            foreach ($data['tags'] as $tid) {
                foreach ($this->tags as $k => $v) {
                    if ($v['id'] == $tid) {
                        $keywords .= $v['name'] . ',';
                    }
                }
            }

            if ($data['cat_id']) {
                $channel         = Categorys::where('id', $data['cat_id'])->value('alias');
                $data['channel'] = $channel;
            }
            $data['update_time'] = time();
            $data['keywords']    = rtrim($keywords, ',') . ',' . $data['title'];
            $data['status']      = $data['status'] && $data['status'] <> '0' ? $data['status'] : 0;
            $data['tags']        = implode(',', $data['tags']);
            $data['actors']      = implode(',', $data['actors']);

            if ($id) {
                Resources::update($data);
            } else {
                $data['clicks']      = mt_rand(300, 500);
                $data['goods']       = mt_rand(11, 99);
                $data['score']       = rand(7, 9) . '.' . rand(0, 9);
                $data['create_time'] = time();
                $where[]             = ['title', '=', $data['title']];
                $res                 = Resources::where($where)->find();
                $res && $this->error('此视频已添加过');
                Resources::insertGetId($data);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success('操作成功');

    }

    //编辑文章
    public function edit()
    {
        if ((!request()->isPost())) {
            $this->info_save();
        }
        $info           = Db::table('videos')->find(input('id'));
        $info['tags']   = explode(',', $info['tags']);
        $info['actors'] = explode(',', $info['actors']);
        View::assign($info);
        return View::fetch('info');
    }
}