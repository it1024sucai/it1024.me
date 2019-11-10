<?php

namespace app\model;

use think\Model;

class Resources extends Model
{
    public function getStatusAttr($value, $data)
    {
        $status = [
            -1 => '<span class="btn btn-xs btn-rounded btn-danger">不通过</span>',
            0  => '<span class="btn btn-xs btn-rounded btn-info">审核中</span>',
            1  => '<span class="btn btn-xs btn-rounded btn-success">已通过</span>'
        ];
        return $status[$data['status']];
    }

    public function getStateAttr($value, $data)
    {
        $status = [
            -1 => '<span class="btn btn-xs btn-rounded btn-danger">未生成</span>',
            0  => '<span class="btn btn-xs btn-rounded btn-info">无需生成</span>',
            1  => '<span class="btn btn-xs btn-rounded btn-success">已生成</span>'
        ];
        return $status[$data['state']];
    }
}
