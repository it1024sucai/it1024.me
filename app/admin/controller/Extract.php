<?php

namespace app\admin\controller;

use app\model\ExtractLogs;
use app\model\Messages;
use app\model\Users;
use think\Db;
use think\facade\View;

class Extract extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $where   = $this->getMap();
        $where[] = ['a.create_time', '>', 0];
        $list    = ExtractLogs::alias('a')->field('a.*,b.username')->join('users b', 'a.uid=b.id')->where($where)
            ->order('create_time desc')->paginate(20,false, ['query'=>input('get.')]);
        View::assign('list', $list);

        return View::fetch();
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }


    public function setstatus()
    {
        $id                = input('id');
        $extract           = ExtractLogs::alias('a')->field('a.*,b.nickname')->join('users b', 'a.uid=b.id')->find($id);
        $status            = $extract->getData('status');
        $extract           = $extract->toArray();
        $extract['status'] = $status;


        if (request()->isAjax()) {
            $data = input('post.');
            if ($data['status'] == 0) {
                return $this->error('审核状态不能为审核中~`');
            }

            try {
                Db::startTrans();
                $title   = $data['status'] > 0 ? '提款申请审核通过' : '提款申请审核不通过';
                $time    = time();
                $message = [
                    'title'       => $title, 'uid_receive' => $extract['uid'], 'content' => $data['remark'],
                    'status'      => 1, 'create_time' => $time, 'update_time' => $time, 'type' => 1
                ];
                $money = Users::where('id', $extract['uid'])->value('money');
                if($money < $extract['money']){
                    $this->error('该用户余额不足~`');
                }

                Messages::insert($message);
                ExtractLogs::update(['id' => $data['id'], 'status' => $data['status']]);

                Users::update(['id' => $extract['uid'], 'money' => $money - $extract['money']]);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
                $this->error('网络繁忙~`');
            }
            $this->success('设置成功~');
        }


        View::assign($extract);
        return View::fetch();
    }

}
