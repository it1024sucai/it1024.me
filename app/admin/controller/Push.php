<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/9/16
 * Time: 13:12
 */

namespace app\admin\controller;


use app\model\Resources;
use util\Rss;

class Push
{

    public function rss(){
        $blog = Resources::field('id,title,channel,content,create_time')->where('status=1')->order('id desc')->limit(200)->select()->toArray();

        $RssConf = array('channelTitle'=>config('web_site_title'),
                         'channelLink'=>'https://www.xiaohongyy.com',
                         'channelDescrīption'=>'gdsag',
                         'copyright'=>'xiaohongyy.com');
        $RSS = new Rss($RssConf);

        $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<urlset>\n";
        foreach ($blog as $k => $v) {
            $s .="\t<url>\n
		\t\t<loc>https://www.xiaohongyy.com/".$v['channel']."/player-".$v['id']."-1</loc>\n
		\t\t<lastmod>".date('Y-m-d H:i:s')."</lastmod>\n
		\t\t<changefreq>always</changefreq>\n
		\t\t<priority>1.0</priority>\n\t</url>\n";

            $RSS->AddItem($v['title'] ,'https://www.xiaohongyy.com/'.$v['channel'].'/player-'.$v['id'].'-1' ,$v['content'] ,date('Y-m-d H:i:s', $v['create_time']) ,'https://www.xiaohongyy.com/'.$v['channel'].'/player-'.$v['id'].'-1' ,$v['actors'],$v['channel']);
        }
        $s .= '</urlset>';
        file_put_contents('./map.xml',$s);
        $RSS->SaveToFile("./rss.xml");
    }

    public function index()
    {
        $count = Resources::count('id');
        $num   = ceil($count / 1000);

        for ($i = 0; $i < $num; $i++) {
            $list = Resources::field('id,channel')->limit($i * 1000, 1000)->select()->toArray();

            foreach ($list as $v) {
                $urls[] = 'https://www.xiaohongyy.com/' . $v['channel'] . '/player-'. $v['id'].'-1';
            }


            //print_r($list);
            //return;
            $api     = 'http://data.zz.baidu.com/urls?site=www.xiaohongyy.com&token=y4LoS7qF8E4MNzyQ';
            $ch      = curl_init();
            $options = array(
                CURLOPT_URL        => $api, CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => implode("\n", $urls), CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            unset($list);
            unset($urls);
            echo $result;
        }

    }
}