<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/11/4
 * Time: 11:42
 */

namespace app\index\controller;


use app\model\Resources;
use it1024sucai\PhpAnalysis;
use think\facade\Db;

class Test
{
    public function index()
    {
        /*$content = '<div id="contentDiv" style="position:relative;font-size:14px;height:auto;z-index:1;zoom:1;line-height:1.7;" class="body">
        <div style="background-color:#f2f2f2;margin:0;padding:20px ;width:100%;height:100%;font-family:Microsoft YaHei;">
            <div style="width:700px;background-color:#fff;margin:0 auto;">
                <div style="height:64px;margin:0;padding:0;width:100%;background-color:#3B97F6;">
                    <a href="http://www.it1024.me" style="display:block;padding-left:20px;padding-top:13px;" rel="noopener" target="_blank">
                    <img style="background: #fff;padding: 2px 10px;border-radius: 10px;" src="javascript:;" width="85" height="37" border="0"></a>
                </div>
                <div style="padding:50px;margin:0;"><div style="font-size:32px;color:#87BD29;line-height:260%;border-bottom:1px dotted #E3E3E3;">it1024素材欢迎您</div>
                    <p style="font-size:14px;color:#333;margin-top:30px;">
                        您的邮箱为：<span style="font-size:14px;color:#333;"><a rel="noopener" target="_blank">659998662@qq.com</a></span>，验证码为：
                    </p>
                    <p style="font-size:34px;color:#55aeff;font-style:italic;">760986</p>
                    <p style="font-size:14px;color:#333;">验证码的有效期为5分钟，请在有效期内输入！</p>
                    <p style="font-size:14px;color:#333;">——it1024素材</p>
                    <p style="font-size:12px;color:#999;border-top:1px dotted #E3E3E3;margin-top:30px;padding-top:30px;">
                        本邮件为系统邮件不能回复，请勿回复。
                    </p>
                </div>
            </div>
        </div>
    </div>';
        $res = send_email('659998662@qq.com','[纯洁博客]：请查收您的验证码',$content);*/

        // 实例化对象
        $phpAnalysisObject = new PhpAnalysis();
        // 设置分词字符串
        $phpAnalysisObject->SetSource('dedecms');
        // 相关配置
        $phpAnalysisObject->SetResultType(1);
        $phpAnalysisObject->differMax = true;

        // 执行分词
        $phpAnalysisObject->StartAnalysis();
        // 获取分词结果
        $result = $phpAnalysisObject->GetFinallyResult(',',5);
        var_dump($result);

//        $data = Resources::alias('a')->field('a.id,b.path,b.ext')->leftJoin('attachments b', 'a.file_id=b.id')->find('10003')
//            ->toArray();
//        if(data['ext'] == 'zip'){
//            $zip  = new \ZipArchive;//新建一个ZipArchive的对象
//            /*
//            通过ZipArchive的对象处理zip文件
//            $zip->open这个方法的参数表示处理的zip文件名。
//            如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
//            */
//            if ($zip->open('.' . $data['path']) === TRUE) {
//                $zip->extractTo('./demo/' . $data['id']);//假设解压缩到在当前路径下images文件夹的子文件夹php
//                $zip->close();//关闭处理的zip文件
//            }
//        }else{
//
//            $rar_file = rar_open('.' . $data['path']) or die("Failed to open Rar archive");
//            $entries = rar_list($rar_file);
//            foreach ($entries as $entry) {
//                //dir/extract/to/换成其他路径即可
//                $entry->extract('./demo/' . $data['id']);
//            }
//            rar_close($rar_file);
//        }
//
//
//
//        //判断项目是否在一级目录，如果不是就移动到一级目录
//        $list = scandir('./demo/' . $data['id']);
//        if (count($list) == 3) {
//            //dump($list[2]);
//            $res = moveFolder('./demo/' . $data['id'] . '/' . $list[2] . '/', './demo/' . $data['id'] . '/');
//            if ($res) {
//                delFolder('./demo/' . $data['id'] . '/' . $list[2]);
//            }
//        }
//        dump($data);


    }
}