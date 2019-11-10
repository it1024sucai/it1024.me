<?php

namespace app\model;


use think\Model;

class Configs extends Model
{
    //
    /**
     * 获取配置信息
     * @param  string $name 配置名
     * @return mixed
     */
    public function getConfig($name = '')
    {
        $configs = self::column('value,type', 'name');



        $result = [];
        foreach ($configs as $config) {
            switch ($config['type']) {
                case 'array':
                    $result[$config['name']] = parse_attr($config['value']);
                    break;
                case 'checkbox':
                    if ($config['value'] != '') {
                        $result[$config['name']] = explode(',', $config['value']);
                    } else {
                        $result[$config['name']] = [];
                    }
                    break;
                default:
                    $result[$config['name']] = $config['value'];
                    break;
            }
        }

        return $name != '' ? $result[$name] : $result;
    }
}
