<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/6/4
 * Time: 11:13
 */

namespace app\admin\controller;


use app\model\Resources;

class Hctapi
{
    function index()
    {
        $data                = input('post.');
        $data['create_time'] = strtotime($data['create_time']);
        $data['update_time'] = $data['create_time'];
        $data['goods']       = mt_rand(15, 55);
        $data['clicks']      = mt_rand(351, 650);
        $data['score']       = mt_rand(7, 9) . '.' . mt_rand(1, 9);

        if (!$data['title'] || !$data['resource']) {
            echo '标题和视频地址不能为空';
            return;
        }

        $where = [
            ['title', '=', $data['title']], ['channel', '=', $data['channel']],
        ];
        $video = Resources::where($where)->find();
        if ($video) {
            $video->time        = $data['time'];
            $video->video       = $data['resource'];
            $video->state       = $data['state'];
            $video->update_time = time();
            $video->save();
        } else {
            Resources::insert($data);
        }
    }


    function download($url, $path = 'uploads/thumbs/')
    {
        $path .= date('Ymd');
        if (!file_exists($path)) {
            mkdir($path, 0766, true);
        }
        $path     .= '/';
        $file     = file_get_contents($url);
        $filename = pathinfo($url, PATHINFO_BASENAME);
        $resource = fopen($path . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);

        $filepath = $path . $filename;
        $image    = \think\Image::open($filepath);
        // 生成缩略图
        $image->thumb(215, 305, 3)->save($filepath);

        return '/' . $filepath;
    }


    function test()
    {

    }
}