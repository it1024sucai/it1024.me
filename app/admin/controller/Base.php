<?php

namespace app\admin\controller;

use app\BaseController;
use app\model\Roles;
use app\model\Sidebars;
use think\facade\Db;
use think\facade\Cache;
use think\facade\View;


class Base extends BaseController
{
    public function initialize()
    {
        /*登录验证*/
        if (!session('admin')['id'])
            exit($this->redirect(url('/login')));

        // 节点权限验证
        if (!Roles::checkAuth()) {
            exit($this->error('沒有訪問权限~'));
        }

        // 获取面包屑导航
        $_location = Sidebars::getLocation('', true);

        /*节点权限判断*/
        if (!empty($_location['error']) && $_location['error']) {
            $this->error($_location['msg']);
        }


        if (!request()->isAjax()) {
            $controller = request()->controller();
            $action     = request()->action();

            // 读取顶部菜单
            $_top_menus = Sidebars::getTopMenu(6, '_top_menus');
            // 读取全部顶级菜单
            $_top_menus_all = Sidebars::getTopMenu('', '_top_menus_all');
            // 获取侧边栏菜单
            $_sidebar_menus = Sidebars::getSidebarMenu();
            $_datas         = [
                '_top_menus' => $_top_menus, '_top_menus_all' => $_top_menus_all, '_sidebar_menus' => $_sidebar_menus,
                '_location'  => $_location, 'controller' => $controller, 'action' => $action,
            ];
            View::assign($_datas);
        }

        /*排序选择变量*/
        $_by = input('_by', 'desc');
        if ($_by == 'asc') {
            $_by = 'desc';
        } elseif ($_by == 'desc') {
            $_by = 'asc';
        }

        View::assign(['_by' => $_by]);
    }

    /**
     * 快速编辑
     * @param array $record 行为日志内容
     * @author 胡卫兵 <659998662@qq.com>
     */
    public function quickEdit()
    {
        $field           = input('name', '');
        $value           = input('value', '');
        $type            = input('type', '');
        $id              = input('pk', '');
        $validate        = input('validate', '');
        $validate_fields = input('validate_fields', '');
        $table           = input('table');

        $field == '' && $this->error('缺少字段名');
        $id == '' && $this->error('缺少主键值');

        $protect_table = ['users', 'roles',];

        // 验证是否操作管理员
        if (in_array($table, $protect_table) && $id == 1) {
            $this->error('禁止操作超级管理员');
        }

        // 验证器
        if ($validate != '') {
            $validate_fields = array_flip(explode(',', $validate_fields));
            if (isset($validate_fields[$field])) {
                $result = $this->validate([$field => $value], $validate . '.' . $field);
                if (true !== $result)
                    $this->error($result);
            }
        }

        switch ($type) {
            // 日期时间需要转为时间戳
            case 'combodate':
                $value = strtotime($value);
                break;
            // 开关
            case 'switch':
                $value = $value == 'true' ? 1 : 0;
                break;
            // 密码
            case 'password':
                $value = Hash::make((string)$value);
                break;
        }

        $result = Db::table($table)->where('id', $id)->update([$field => $value]);

        cache('configs', null);
        if (false !== $result) {
            // 记录行为日志
            /*if (!empty($record)) {
                call_user_func_array('action_log', $record);
            }*/
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }


    /**
     * 禁用
     * @param array $record 行为日志内容
     * @author 胡卫兵 <659998662@qq.com>
     * @return mixed
     */
    public function disable()
    {
        return $this->setStatus('disable');
    }

    /**
     * 启用
     * @param array $record 行为日志内容
     * @author 胡卫兵 <659998662@qq.com>
     * @return mixed
     */
    public function enable()
    {
        return $this->setStatus('enable');
    }

    /**
     * 删除
     * @param array $record 行为日志内容
     * @author 胡卫兵 <659998662@qq.com>
     * @return mixed
     */
    public function delete()
    {
        return $this->setStatus('delete');
    }

    /**
     * 删除
     * @param array $record 行为日志内容
     * @author 胡卫兵 <659998662@qq.com>
     * @return mixed
     */
    public function status()
    {
        $table = input('table');
        $ids   = request()->isPost() ? request()->post('ids') : input('ids');
        $ids   = (array)$ids;
        $Model = Db::table($table);

        empty($ids) && $this->error('缺少主键');
        $result = $Model->whereIn('id', $ids)->update(['status' => input('value')]);

        if ($result) {
            shell_exec('php think cache:clear');
            // 记录行为日志
            /*if (!empty($record)) {
                call_user_func_array('action_log', $record);
            }*/
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 设置状态
     * 禁用、启用、删除都是调用这个内部方法
     * @param string $type 操作类型：enable,disable,delete
     * @param array $record 行为日志内容
     * @author 胡卫兵 <659998662@qq.com>
     * @return mixed
     */
    public function setStatus($type)
    {
        $ids   = request()->isPost() ? request()->post('ids') : input('ids');
        $ids   = (array)$ids;
        $table = input('table');
        $field = input('field') ?: 'status';

        empty($ids) && $this->error('缺少主键');

        $Model         = Db::table($table);
        $protect_table = ['users', 'roles',];

        // 禁止操作核心表的主要数据
        if (in_array($table, $protect_table) && in_array('1', $ids)) {
            $this->error('禁止操作');
        }

        $result = false;
        switch ($type) {
            case 'disable': // 禁用
                $result = $Model->whereIn('id', $ids)->update([$field => 0]);
                break;
            case 'enable': // 启用
                $result = $Model->whereIn('id', $ids)->update([$field => 1]);
                break;
            case 'delete': // 删除
                $result = $Model->whereIn('id', $ids)->delete();
                break;
            default:
                $this->error('非法操作');
                break;
        }

        if ($result) {
            shell_exec('php think cache:clear');
            // 记录行为日志
            /*if (!empty($record)) {
                call_user_func_array('action_log', $record);
            }*/
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 入库操作
     * @return array
     */
    public function save($table)
    {
        $data = input();
        $id   = $data['id'];
        $time = time();
        unset($data['id']);
        $data['update_time'] = $time;
        $data['status']      = $data['status'] == 'on' ? 1 : 0;

        if ($id) {
            $res = Db::table($table)->where('id', $id)->update($data);
        } else {
            $data['create_time'] = $time;
            $res                 = Db::table($table)->insert($data);
        }

        /*如果是操作 配置表或节点表 就清空缓存*/
        if (in_array($table, ['configs', 'sidebars'])) {
            Cache::clear();
        }

        $res ? $this->success('操作成功') : $this->error('操作失败');
    }

    /**
     * 获取筛选条件
     * @author 胡卫兵 <659998662@qq.com>
     *
     * @return array
     */
    /**
     * 获取筛选条件
     * @return array
     */
    final protected function getMap()
    {
        $search_field     = input('param.search_field/s', '');
        $keyword          = input('param.keyword/s', '');
        $filter           = input('param._filter/s', '');
        $filter_content   = input('param._filter_content/s', '');
        $filter_time      = input('param._filter_time/s', '');
        $filter_time_from = input('param._filter_time_from/s', '');
        $filter_time_to   = input('param._filter_time_to/s', '');
        $select_field     = input('param._select_field/s', '');
        $select_value     = input('param._select_value/s', '');
        $search_area      = input('param._s', '');
        $search_area_op   = input('param._o', '');

        $map = [];

        // 搜索框搜索
        if ($search_field != '' && $keyword !== '') {
            $map[] = [$search_field, 'like', "%$keyword%"];
        }

        // 下拉筛选
        if ($select_field != '') {
            $select_field = array_filter(explode('|', $select_field), 'strlen');
            $select_value = array_filter(explode('|', $select_value), 'strlen');
            foreach ($select_field as $key => $item) {
                if ($select_value[$key] != '_all') {
                    $map[] = [$item, $select_value[$key]];
                }
            }
        }

        // 时间段搜索
        if ($filter_time != '' && $filter_time_from != '' && $filter_time_to != '') {
            $map[] = [$filter_time, 'between time', [$filter_time_from . ' 00:00:00', $filter_time_to . ' 23:59:59']];
        }

        // 表头筛选
        if ($filter != '') {
            $filter         = array_filter(explode('|', $filter), 'strlen');
            $filter_content = array_filter(explode('|', $filter_content), 'strlen');
            foreach ($filter as $key => $item) {
                if (isset($filter_content[$key])) {
                    $map[] = [$item, 'in', $filter_content[$key]];
                }
            }
        }

        // 搜索区域
        if ($search_area != '') {
            $search_area    = explode('|', $search_area);
            $search_area_op = explode('|', $search_area_op);
            foreach ($search_area as $key => $item) {
                list($field, $value) = explode('=', $item);
                $op = explode('=', $search_area_op[$key]);
                if ($value != '') {
                    switch ($op[1]) {
                        case 'like':
                            $map[$field] = ['like', "%$value%"];
                            break;
                        case 'between time':
                        case 'not between time':
                            $value = explode(' - ', $value);
                            if ($value[0] == $value[1]) {
                                $value[0] = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                                $value[1] = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                            }
                        default:
                            $map[] = [$field, $op[1], $value];
                    }
                }
            }
        }
        return $map;
    }

    /**
     * 获取字段排序
     *
     * @param string $extra_order 额外的排序字段
     * @param bool $before 额外排序字段是否前置
     *
     * @author 胡卫兵 <659998662@qq.com>
     * @return string
     */
    final protected function getOrder($extra_order = '', $before = false)
    {
        $order = input('_order', '');
        $by    = input('_by', '');
        if ($order == '' || $by == '') {
            return $extra_order;
        }
        if ($extra_order == '') {
            return $order . ' ' . $by;
        }
        if ($before) {
            return $extra_order . ',' . $order . ' ' . $by;
        } else {
            return $order . ' ' . $by . ',' . $extra_order;
        }
    }
}
