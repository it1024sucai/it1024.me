<?php

namespace app\model;

use think\Model;

class RechargeLogs extends Model
{
    public function getStatusAttr($value, $data)
    {
        $status = [
            -1 => '<span class="btn btn-xs btn-rounded btn-danger">支付失败</span>',
            0  => '<span class="btn btn-xs btn-rounded btn-info">未支付</span>',
            1  => '<span class="btn btn-xs btn-rounded btn-success">支付成功</span>'
        ];
        return $status[$data['status']];
    }
}
