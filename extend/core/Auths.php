<?php
/**
 * Created by PhpStorm.
 * User: 659998662
 * Date: 2018/12/15
 * Time: 20:43
 */

namespace core;

use think\facade\Cache;

class Auths
{
    /**
     * 生成每次请求的sign
     * @param array $data
     * @return string
     */
    public static function setSign($data = [])
    {
        // 1 按字段排序
        ksort($data);
        // 2拼接字符串数据  &
        $string = http_build_query($data);
        // 3通过aes来加密
        $string = (new AES())->encrypt($string);
        return $string;
    }

    /**
     * 检查sign是否正常
     * @param array $data
     * @param $data
     * @return boolen
     */
    public static function checkSignPass($data, $sgin)
    {
        $str = (new AES())->decrypt($data['sign']);
        if (empty($str)) {
            return false;
        }

        if ($data['sign'] !== $sgin) {
            return false;
        }

        // diid=xx&app_type=3
        $arr = json_decode($str, true);

        if (!config('app_debug')) {
            if ((time() - intval($arr['time'])) > config('api.app_sign_cache_time')) {
                return false;
            }
            // echo Cache::get($data['sign']);exit;
            // 唯一性判定
            if (Cache::get($data['sign'])) {
                return false;
            }
        }
        return true;
    }
}