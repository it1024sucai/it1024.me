<?php
declare (strict_types=1);

namespace app\index\controller;

use app\model\Resources;
use it1024sucai\PhpAnalysis;
use think\facade\Db;
use think\facade\View;

class Search extends Common
{
    public function getPa($wd)
    {
        // 实例化对象
        $pa = new PhpAnalysis();
        // 设置分词字符串
        $pa->SetSource($wd);
        // 相关配置
        $pa->SetResultType(1);
        $pa->differMax = true;
        // 执行分词
        $pa->StartAnalysis();
        // 获取分词结果
        $result = $pa->GetFinallyResult(',');
        if ($result) {
            $keywords = getSourceById($result)['value'] . ',' . config('app.web_site_keywords');
            View::assign('keywords', $keywords);
        }
        return explode(',', $result);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $wd      = input('wd/s');
        $page    = input('page/d');
        $channel = input('channel/s') ?: 'index';

        $map1['a.status'] = 1;
        $path             = $page ? '/search-index-p' . $page . '.html' : '/search-index.html';
        $page_config      = [
            'list_rows' => 12, 'path' => $path, 'page' => $page,
        ];

        if ($channel !== 'index')
            $map1['a.channel'] = $channel;

        $path                = $page ? '/search-' . $channel . '-p' . $page . '.html' : '/search-' . $channel . '.html';
        $page_config['path'] = $path;

        if ($wd) {
            $path                = $page ? '/search-' . $channel . '-' . $wd . '-p' . $page . '.html' : '/search-' . $channel . '-' . $wd . '.html';
            $page_config['path'] = $path;
            $wds                 = $this->getPa($wd);
            foreach ($wds as $v) {
                $map2[] = ['title','like', '%' . $v . '%'];
            }

            $list = Resources::alias('a')->field('a.id,a.title,a.create_time,a.channel,a.description,b.thumb')
                ->leftJoin('attachments b', 'a.thumb=b.id')->whereOr($map2)->where($map1)->order('create_time desc')
                ->paginate($page_config);
        } else {
            $list = Resources::alias('a')->field('a.id,a.title,a.create_time,a.channel,a.description,b.thumb')
                ->leftJoin('attachments b', 'a.thumb=b.id')->where($map1)->order('create_time desc')
                ->paginate($page_config);
        }


        //dump(Db::table('resources')->getLastSql());

        $path_tpl = $list->render();
        $list     = $list->toArray();

        //dump($list);

        $data = [
            'channel' => $channel, 'wd' => $wd, 'wds' => $wds, 'list' => $list, 'path_tpl' => $path_tpl
        ];

        View::assign($data);
        return View::fetch();
    }

    public function tags()
    {
        $tag = input('tag/s');
        if (!$tag) {
            $this->error('参数错误~');
        }

        $map  = [
            ['a.status', '=', 1], ['tags', 'like', '%' . $tag . '%']
        ];
        $list = Resources::alias('a')->field('a.id,a.title,a.create_time,a.channel,a.description,b.thumb')
            ->leftJoin('attachments b', 'a.thumb=b.id')->where($map)->order('create_time desc')->paginate(12);

        $path_tpl = $list->render();
        $list     = $list->toArray();
        View::assign('list', $list);
        return View::fetch();
    }
}
