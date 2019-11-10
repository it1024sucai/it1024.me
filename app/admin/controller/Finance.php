<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/6/10
 * Time: 10:49
 */

namespace app\admin\controller;


use app\model\ExtractLogs;
use app\model\RebateLogs;
use app\model\RechargeLogs;
use think\facade\View;

class Finance extends Base
{
    public function index()
    {

        $recharges = RechargeLogs::where('status', 1)->sum('price');
        $rebates   = RebateLogs::sum('money');
        $extracts  = ExtractLogs::where('status', 1)->sum('money');
        $profits   = $recharges - $rebates;

        $data = [
            'recharges' => $recharges, 'rebates' => $rebates, 'extracts' => $extracts, 'profits' => $profits
        ];
        View::assign($data);
        return View::fetch();
    }
}