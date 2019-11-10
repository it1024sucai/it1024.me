<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/10/25
 * Time: 17:20
 */
use think\facade\Route;


// 完整域名绑定到admin模块
Route::domain('admin', ':\app\admin\controller');