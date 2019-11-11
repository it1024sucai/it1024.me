<?php

namespace util;

/**
 * 树结构生成类
 */
class Tree
{
    /**
     * 配置参数
     * @var array
     */
    protected static $instance;
    private static   $out_data = null;
    private static   $in_data  = null;
    protected static $config   = [
        'id'    => 'id',    // id名称
        'pid'   => 'pid',   // pid名称
        'title' => 'title', // 标题名称
        'child' => 'child', // 子元素键名
        'icons' => ['│', '├', '└'],   // 层级标记
        'step'  => 4,       // 层级步进数量
    ];

    /**
     * 架构函数
     * @param array $config
     */
    public function __construct($config = [])
    {
        self::$config = array_merge(self::$config, $config);
    }

    /**
     * 配置参数
     * @param  array $config
     * @return object
     */
    public static function config($config = [])
    {
        if (!empty($config)) {
            $config = array_merge(self::$config, $config);
        }
        if (is_null(self::$instance)) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    /**
     * 将数据集格式化成层次结构
     * @param array/object $lists 要格式化的数据集，可以是数组，也可以是对象
     * @param int $pid 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
     * @author 胡卫兵 <659998662@qq.com>
     * @return array
     */
    public static function toLayer($lists = [], $pid = 0, $max_level = 0, $curr_level = 0)
    {
        $trees = [];
        $lists = array_values($lists);
        foreach ($lists as $key => $value) {
            if ($value[self::$config['pid']] == $pid) {
                if ($max_level > 0 && $curr_level == $max_level) {
                    return $trees;
                }
                unset($lists[$key]);
                $child = self::toLayer($lists, $value[self::$config['id']], $max_level, $curr_level + 1);
                if (!empty($child)) {
                    $value[self::$config['child']] = $child;
                }
                $trees[] = $value;
            }
        }
        return $trees;
    }

    /**
     * 根据子节点返回所有父节点
     * @param  array $lists 数据集
     * @param  string $id 子节点id
     * @return array
     */
    public static function getParents($lists = [], $id = '')
    {
        $trees = [];
        foreach ($lists as $value) {
            if ($value[self::$config['id']] == $id) {
                $trees[] = $value;
                $trees = array_merge(self::getParents($lists, $value[self::$config['pid']]), $trees);
            }
        }
        return $trees;
    }

    /**
     * 获取所有子节点id
     * @param  array $lists 数据集
     * @param  string $pid 父级id
     * @return array
     */
    public static function getChildsId($lists = [], $pid = '')
    {
        $result = [];
        foreach ($lists as $value) {
            if ($value[self::$config['pid']] == $pid) {
                $result[] = $value[self::$config['id']];
                $result = array_merge($result, self::getChildsId($lists, $value[self::$config['id']]));
            }
        }
        return $result;
    }

    /**
     * 获取所有子节点
     * @param  array $lists 数据集
     * @param  string $pid 父级id
     * @return array
     */
    public static function getChilds($lists = [], $pid = '')
    {
        $result = [];
        foreach ($lists as $value) {
            if ($value[self::$config['pid']] == $pid) {
                $result[] = $value;
                $result = array_merge($result, self::getChilds($lists, $value[self::$config['id']]));
            }
        }
        return $result;
    }


    /**
     * 将数据集格式化成列表结构
     * @param  array|object $lists 要格式化的数据集，可以是数组，也可以是对象
     * @param  integer $pid 父级id
     * @param  integer $level 级别
     * @return array 列表结构(一维数组)
     */
    public static function toList($lists, $id = 'id', $pid = 'pid')
    {
        self::$in_data = $lists;
        self::$config['id'] = $id;
        self::$config['pid'] = $pid;
        self::$out_data = array();
        self::get();
        return self::$out_data;
    }

    private static function get($start_id = 0, $remove_id = 0, $dept = 0)
    {
        $dept++;
        $temp = array();
        foreach (self::$in_data as $value) {
            $kid = $value[self::$config['id']];
            $pid = $value[self::$config['pid']];
            if ($remove_id != null && $kid == $remove_id) {
                continue;
            }
            if ($pid == $start_id) {
                $temp[] = $value;
            }
        }
        $count = count($temp);
        foreach ($temp as $key => $value) {
            $kid = $value[self::$config['id']];
            $value['path'] = self::path($count, $key, $dept);
            self::$out_data[] = $value;
            self::get($kid, $remove_id, $dept);
        }
    }

    private static function path($count, $idx, $dept)
    {
        for ($i = 1; $i < $dept; $i++) {
            $icons[] = self::$config['icons'][0];
        }
        if (count(self::$out_data) > 1 && $count == ($idx + 1)) {
            $icons[] = self::$config['icons'][2];
        } else {
            $icons[] = self::$config['icons'][1];
        }
        return join(' ', $icons);
    }
}