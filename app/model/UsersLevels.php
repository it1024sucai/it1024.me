<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/11/2
 * Time: 8:44
 */

namespace app\model;


use think\Model;

class UsersLevels extends Model
{
    public static function getLevel($exp){
        return self::field('level,level_name')->where('exp','<=',$exp)->order('exp desc')->find()->toArray();
    }
}