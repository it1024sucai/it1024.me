<?php

namespace app\admin\controller;

use app\model\Configs;
use think\facade\View;

class System extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($group = 'base')
    {
        //dump(config());
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();

            if (isset(config('app.config_group')[$group])) {
                // 查询该分组下所有的配置项名和类型
                $items = Configs::where('group', $group)->where('status', 1)->column('name,type');

                foreach ($items as $name => $type) {
                    if (!isset($data[$name])) {
                        switch ($type) {
                            // 开关
                            case 'switch':
                                $data[$name] = 0;
                                break;
                            case 'checkbox':
                                $data[$name] = '';
                                break;
                        }
                    } else {
                        // 如果值是数组则转换成字符串，适用于复选框等类型
                        if (is_array($data[$name])) {
                            $data[$name] = implode(',', $data[$name]);
                        }
                        switch ($type) {
                            // 开关
                            case 'switch':
                                $data[$name] = 1;
                                break;
                            // 日期时间
                            case 'date':
                            case 'time':
                            case 'datetime':
                                $data[$name] = strtotime($data[$name]);
                                break;
                        }
                    }
                    Configs::where('name', $name)->update(['value' => $data[$name]]);
                }
            }
            cache('system_config', null);

            // 记录行为
            $this->success('更新成功', url('index', ['group' => $group]));
        } else {
            // 配置分组信息
            $list_group = config('app.config_group');


            $tab_list = [];
            foreach ($list_group as $key => $value) {
                $tab_list[$key]['title'] = $value;
                $tab_list[$key]['url'] = url('index', ['group' => $key]);
            }

            if (isset(config('app.config_group')[$group])) {
                // 查询条件
                $map[] = ['group', '=', $group];
                $map[] = ['status', '=', 1];

                // 数据列表
                $data_list = Configs::where($map)
                    ->order('sort asc,id asc')
                    ->column('name,title,tips,type,value,options');

                foreach ($data_list as &$value) {
                    // 解析options
                    if ($value['options'] != '') {
                        $value['options'] = parse_attr($value['options']);
                    }
                }
            }
            View::assign(['tab_list'=>$tab_list,'data_list'=>$data_list,'group'=>$group]);
        }
        return View::fetch();
    }
}
