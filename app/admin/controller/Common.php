<?php

namespace app\admin\controller;


use app\BaseController;
use app\model\Attachments;
use think\Container;
use think\facade\Db;
use think\facade\Cache;
use think\Image;

class Common extends BaseController
{
    //清除缓存
    public function clearCache()
    {
        if (!empty(config('clear_cache_type'))) {
            $app         = Container::get('app');
            $runtimePath = $app->getRuntimePath();
            foreach (config('clear_cache_type') as $item) {
                del_dir($runtimePath . $item);
            }
            Cache::clear();
            $this->success('清空成功');
        } else {
            $this->error('请在系统设置中选择需要清除的缓存类型');
        }
    }

    /**
     * 获取筛选数据
     * @param string $table 表名
     * @param string $field 字段名
     * @param array $map 查询条件
     * @param string $options 选项，用于显示转换
     * @param string $list 选项缓存列表名称
     * @author 胡卫兵 <6599986622@qq.com>
     * @return \think\response\Json
     */
    public function filterlist()
    {
        $table   = input('table');
        $field   = input('field');
        $options = input('options');
        $list    = input('list');
        if ($list != '') {
            $result = ['code' => 1, 'msg' => '请求成功', 'list' => $list];
            return $result;
        }
        if ($table == '') {
            return ['code' => 0, 'msg' => '缺少表名'];
        }
        if ($field == '') {
            return ['code' => 0, 'msg' => '缺少字段'];
        }

        if (strpos($table, '/')) {
            $data_list = model($table)->whereNotNull($field)->group($field)->column($field);
        } else {
            $data_list = Db::table($table)->whereNotNull($field)->group($field)->column($field);
        }

        if ($data_list === false) {
            return json(['code' => 0, 'msg' => '查询失败']);
        }

        if ($data_list) {
            if ($options != '') {
                // 从缓存获取选项数据
                $options = config($options);
                if ($options) {
                    $temp_data_list = [];
                    foreach ($data_list as $item) {
                        $temp_data_list[$item] = isset($options[$item]) ? $options[$item] : '';
                    }
                    $data_list = $temp_data_list;
                } else {
                    $data_list = parse_array($data_list);
                }
            } else {
                $data_list = parse_array($data_list);
            }

            $result = [
                'code' => 1, 'msg' => '请求成功', 'list' => $data_list
            ];
            return json($result);
        } else {
            return json(['code' => 0, 'msg' => '查询不到数据']);
        }
    }


    /**
     * 处理Jcrop图片裁剪
     */
    private function jcrop()
    {
        $file_path = request()->post('path', '');
        $cut_info  = request()->post('cut', '');
        $thumb     = request()->post('thumb', '');
        $watermark = request()->post('watermark', '');
        $module    = request()->param('module', '');

        // 上传图片
        if ($file_path == '') {
            $file = request()->file('file');
            if (!is_dir(config('upload_temp_path'))) {
                mkdir(config('upload_temp_path'), 0766, true);
            }
            $info = $file->move(config('upload_temp_path'), $file->hash('md5'));
            if ($info) {
                return json(['code' => 1, 'src' => PUBLIC_PATH . 'uploads/temp/' . $info->getFilename()]);
            } else {
                $this->error('上传失败');
            }
        }

        $file_path = config('upload_temp_path') . str_replace(PUBLIC_PATH . 'uploads/temp/', '', $file_path);

        if (is_file($file_path)) {
            // 获取裁剪信息
            $cut_info = explode(',', $cut_info);

            // 读取图片
            $image = Image::open($file_path);

            $dir_name = date('Ymd');
            $file_dir = config('upload_path') . DS . 'images/' . $dir_name . '/';
            if (!is_dir($file_dir)) {
                mkdir($file_dir, 0766, true);
            }
            $file_name     = md5(microtime(true)) . '.' . $image->type();
            $new_file_path = $file_dir . $file_name;

            // 裁剪图片
            $image->crop($cut_info[0], $cut_info[1], $cut_info[2], $cut_info[3], $cut_info[4], $cut_info[5])
                ->save($new_file_path);

            // 水印功能
            if ($watermark == '') {
                if (config('upload_thumb_water') == 1 && config('upload_thumb_water_pic') > 0) {
                    $this->create_water($new_file_path, config('upload_thumb_water_pic'));
                }
            } else {
                if (strtolower($watermark) != 'close') {
                    list($watermark_img, $watermark_pos, $watermark_alpha) = explode('|', $watermark);
                    $this->create_water($new_file_path, $watermark_img, $watermark_pos, $watermark_alpha);
                }
            }

            // 是否创建缩略图
            $thumb_path_name = '';
            if ($thumb == '') {
                if (config('upload_image_thumb') != '') {
                    $thumb_path_name = $this->create_thumb($new_file_path, $dir_name, $file_name);
                }
            } else {
                if (strtolower($thumb) != 'close') {
                    list($thumb_size, $thumb_type) = explode('|', $thumb);
                    $thumb_path_name = $this->create_thumb($new_file_path, $dir_name, $file_name, $thumb_size, $thumb_type);
                }
            }

            // 保存图片
            $file      = new File($new_file_path);
            $file_info = [
                'uid'    => session('user_auth.uid'), 'name' => $file_name, 'mime' => $image->mime(),
                'path'   => 'uploads/images/' . $dir_name . '/' . $file_name, 'ext' => $image->type(),
                'size'   => $file->getSize(), 'md5' => $file->hash('md5'), 'sha1' => $file->hash('sha1'),
                'thumb'  => $thumb_path_name, 'module' => $module, 'width' => $image->width(),
                'height' => $image->height()
            ];

            if ($file_add = Attachments::create($file_info)) {
                // 删除临时图片
                unlink($file_path);
                // 返回成功信息
                return json([
                                'code'  => 1, 'id' => $file_add['id'], 'src' => PUBLIC_PATH . $file_info['path'],
                                'thumb' => $thumb_path_name == '' ? '' : PUBLIC_PATH . $thumb_path_name,
                            ]);
            } else {
                $this->error('上传失败');
            }
        }
        $this->error('文件不存在');
    }

    /**
     * 创建缩略图
     * @param string $file 目标文件，可以是文件对象或文件路径
     * @param string $dir 保存目录，即目标文件所在的目录名
     * @param string $save_name 缩略图名
     * @param string $thumb_size 尺寸
     * @param string $thumb_type 裁剪类型* @returnstring 缩略图路径
     */
    private function create_thumb($file = '', $dir = '', $save_name = '', $thumb_size = '', $thumb_type = '')
    {
        // 获取要生成的缩略图最大宽度和高度
        $thumb_size = $thumb_size == '' ? config('upload_image_thumb') : $thumb_size;
        list($thumb_max_width, $thumb_max_height) = explode(',', $thumb_size);
        // 读取图片
        $image = Image::open($file);
        // 生成缩略图
        $thumb_type = $thumb_type == '' ? config('upload_image_thumb_type') : $thumb_type;
        $image->thumb($thumb_max_width, $thumb_max_height, $thumb_type);
        // 保存缩略图
        $thumb_path = config('upload_path') . '/images/' . $dir . '/thumb/';
        if (!is_dir($thumb_path)) {
            mkdir($thumb_path, 0766, true);
        }
        $thumb_path_name = $thumb_path . $save_name;
        $image->save($thumb_path_name);
        $thumb_path_name = 'uploads/images/' . $dir . '/thumb/' . $save_name;
        return $thumb_path_name;
    }

    /**
     * 添加水印
     * @param string $file 要添加水印的文件路径
     * @param string $watermark_img 水印图片id
     * @param string $watermark_pos 水印位置
     * @param string $watermark_alpha 水印透明度
     */
    private function create_water($file = '', $watermark_img = '', $watermark_pos = '', $watermark_alpha = '')
    {
        $path            = Attachments::getFilePath($watermark_img, 1);
        $thumb_water_pic = realpath(ROOT_PATH . 'public/' . $path);
        if (is_file($thumb_water_pic)) {
            // 读取图片
            $image = Image::open($file);
            // 添加水印
            $watermark_pos   = $watermark_pos == '' ? config('upload_thumb_water_position') : $watermark_pos;
            $watermark_alpha = $watermark_alpha == '' ? config('upload_thumb_water_alpha') : $watermark_alpha;
            $image->water($thumb_water_pic, $watermark_pos, $watermark_alpha);
            // 保存水印图片，覆盖原图
            $image->save($file);
        }
    }

}
