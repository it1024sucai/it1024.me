<?php

namespace app\admin\controller;

use App\Model\Sidebars;
use think\facade\View;

class Sidebar extends Base
{
    public $list = [];

    public function initialize()
    {
        parent::initialize();
        $list = Sidebars::getSidebars();
        $this->list = $list;
        //树形处理
        $sidebars = Sidebars::getMenuTree();

        View::assign('sidebars', $sidebars);
    }

    /**
     * 列表页面
     *
     */
    public function index()
    {
        $group = input('group','admin');
        // 保存模块排序
        if (request()->isAjax()) {
            $modules = $_POST['id'];
            if ($modules) {
                $data = [];
                $k = 1;
                foreach ($modules as $module) {
                    $data[] = [
                        'id' => $module,
                        'sort' => $k++
                    ];
                }
                if (false !== (new Sidebars)->saveAll($data)) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            }
        }
        if ($group != 'module-sort') {
            foreach ($this->list as $k => $v) {
                if ($v['module'] == $group)
                    $list[$k] = $v;
                else
                    continue;
            }
            $max_level = input('max', 0);

            $menus = $this->getNestMenu($list, $max_level);
            View::assign('menus', $menus);
        }
        $data = [
            'group' => $group
        ];
        return View::fetch('',$data);
    }

    /**
     * 创建页面
     */
    public function create()
    {
        if (request()->isAjax()) {
            return $this->save();
        }
        $info = [];
        $pid = input('pid');
        if($pid){
            $info['pid'] = $pid;
            $info['module'] = Sidebars::where('id',$pid)->value('module');
        }

        return View::fetch('info',$info);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        if (\request()->isAjax()) {
            return $this->save();
        }
        $id = input('id');
        $info = Sidebars::where('id', $id)->find()->toArray();
        return view('info', $info);
    }

    /**
     * 入库操作
     * @return array
     */
    public function save($table ='')
    {
        $data = input('post.');
        $id = $data['id'];
        $time = time();
        unset($data['id']);
        $data['update_time'] = $time;

        try {
            if (!empty($data['menus']) && empty($data['id'])) {
                $menus = $this->parseMenu($data['menus']);
                foreach ($menus as $menu) {
                    Sidebars::where('id',$menu['id'])->update($menu);
                }
                return ['msg'=>'操作成功', 'url'=>url('/sidebar/index', ['group' => $data['module']]),'code'=>1];
            }else{
                if ($id) {
                    $res = Sidebars::where('id', $id)->update($data);
                    $res = $res ? ['code' => 1, 'msg' => '更新成功'] : ['code' => 0, 'msg' => '更新失败'];
                } else {
                    $data['create_time'] = $time;
                    $res = Sidebars::insertGetId($data);
                    $res = $res ? ['code' => 1, 'msg' => '添加成功'] : ['code' => 0, 'msg' => '添加失败'];
                }
            }
        } catch (\Exception $e) {
            $res = ['code' => 0, 'msg' => '操作失败'];
        }

        return $res;
    }


    /**
     * 获取嵌套式节点
     * @param array $lists 原始节点数组
     * @param int $pid 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
     * @author 胡卫兵 <659998662@qq.com>
     * @return string
     */
    private function getNestMenu($lists = [], $max_level = 0, $pid = 0, $curr_level = 1)
    {
        $result = '';
        foreach ($lists as $key => $value) {
            if ($value['pid'] == $pid) {
                $disable = $value['status'] == 0 ? 'dd-disable' : '';

                // 组合节点
                $result .= '<li class="dd-item dd3-item ' . $disable . '" data-id="' . $value['id'] . '">';
                $result .= '<div class="dd-handle dd3-handle">拖拽</div><div class="dd3-content"><i class="' . $value['icon'] . '"></i> ' . $value['title'];
                if ($value['url_value'] != '') {
                    $result .= '<span class="link"><i class="fa fa-link"></i> ' . $value['url_value'] . '</span>';
                }
                $result .= '<div class="action">';
                $result .= '<a href="' . url('/sidebar/create', ['module' => $value['module'], 'pid' => $value['id']]) . '" data-toggle="tooltip" data-original-title="新增子节点"><i class="list-icon fa fa-plus fa-fw"></i></a><a href="' . url('/sidebar/edit', ['id' => $value['id']]) . '" data-toggle="tooltip" data-original-title="编辑"><i class="list-icon fa fa-pencil fa-fw"></i></a>';
                if ($value['status'] == 0) {
                    // 启用
                    $result .= '<a href="javascript:void(0);" data-ids="' . $value['id'] . '" class="enable" data-toggle="tooltip" data-original-title="启用"><i class="list-icon fa fa-check-circle-o fa-fw"></i></a>';
                } else {
                    // 禁用
                    $result .= '<a href="javascript:void(0);" data-ids="' . $value['id'] . '" class="disable" data-toggle="tooltip" data-original-title="禁用"><i class="list-icon fa fa-ban fa-fw"></i></a>';
                }
                $result .= '<a href="' . url('/sidebar/delete', ['ids' => $value['id'], 'table' => 'sidebars']) . '" data-toggle="tooltip" data-original-title="删除" class="ajax-get confirm"><i class="list-icon fa fa-times fa-fw"></i></a></div>';
                $result .= '</div>';

                if ($max_level == 0 || $curr_level != $max_level) {
                    unset($lists[$key]);
                    // 下级节点
                    $children = $this->getNestMenu($lists, $max_level, $value['id'], $curr_level + 1);
                    if ($children != '') {
                        $result .= '<ol class="dd-list">' . $children . '</ol>';
                    }
                }

                $result .= '</li>';
            }
        }
        return $result;
    }

    /**
     * 递归解析节点
     * @param array $menus 节点数据
     * @param int $pid 上级节点id
     * @author 胡卫兵 <659998662@qq.com>
     * @return array 解析成可以写入数据库的格式
     */
    private function parseMenu($menus = [], $pid = 0)
    {
        $sort = 1;
        $result = [];
        foreach ($menus as $menu) {
            $result[] = [
                'id' => (int)$menu['id'],
                'pid' => (int)$pid,
                'sort' => $sort,
            ];
            if (isset($menu['children'])) {
                $result = array_merge($result, $this->parseMenu($menu['children'], $menu['id']));
            }
            $sort++;
        }
        return $result;
    }
}
