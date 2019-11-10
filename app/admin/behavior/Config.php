<?php


namespace app\admin\behavior;

use think\facade\Db;

/**
 * 初始化配置信息行为
 * 将系统配置信息合并到本地配置
 * @package app\common\behavior
 * @author 胡卫兵 <6599986622@qq.com>
 */
class Config {
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params 行为参数
     * @return void
     */
    public function handle() {
        // 如果是安装操作，直接返回
        if (defined('BIND_MODULE') && BIND_MODULE === 'install') return;


        // 获取入口目录
        $base_file = request()->baseFile();
        $base_dir  = substr($base_file, 0, strripos($base_file, '/') + 1);
        define('PUBLIC_PATH', $base_dir);

        // 读取系统配置
        $system_config = cache('system_config');
        if (!$system_config) {
            $configs = Db::table('configs')->column('value,type', 'name');
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
            $system_config = $result;
            /*// 所有模型配置
            $module_config = Module::where('config', 'neq', '')->column('config', 'name');
            foreach ($module_config as $module_name => $config) {
                $system_config[strtolower($module_name) . '_config'] = json_decode($config, true);
            }*/
            // 非开发模式，缓存系统配置
            if ($system_config['app_debug'] == 0) {
                cache('system_config', $system_config);
            }
        }

        /*echo '<pre>';
        print_r($system_config);
        exit();*/

        $paginate                               = config('paginate.');
        $system_config['paginate']['list_rows'] = $system_config['list_rows'] ?: $paginate['list_rows'];
        $system_config['paginate']['var_page']  = $system_config['var_page'] ?: $paginate['var_page'];
        $system_config['paginate']['type']      = $system_config['type'] ?: $paginate['type'];


        // 设置配置信息
        config($system_config, 'app');

        config($system_config['paginate'], 'paginate');

    }
}
