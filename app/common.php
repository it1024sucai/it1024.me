<?php
/**
 * Created by PhpStorm.
 * User: 659998662
 * Date: 2018/11/5
 * Time: 21:53
 */

use \app\model\SourceAttrs;

//错误级别设置
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);




/**
 * 远程获取数据，POST模式
 * @param $url 指定URL完整路径地址
 * @param $param 请求的数据
 * return 远程输出的数据
 */
function getHttpResponsePOST($url = '', $param = array()) {
    if (empty($url) || empty($param)) {
        return false;
    }
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}

/**
 * 远程获取数据，GET模式
 * 注意：
 * @param $url 指定URL完整路径地址
 * @param $header 头部
 * return 远程输出的数据
 */
function getHttpResponseGET($url,$header=null) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    if(!empty($header)){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    // echo curl_getinfo($curl);
    curl_close($curl);
    unset($curl);
    return $output;
}

/**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
function getfiles($path, $allowFiles, &$files = array())
{
    if (!is_dir($path))
        return null;
    if (substr($path, strlen($path) - 1) != '/')
        $path .= '/';
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $path2 = $path . $file;
            if (is_dir($path2)) {
                getfiles($path2, $allowFiles, $files);
            } else {
                if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
                    $files[] = array(
                        'url' => substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])), 'mtime' => filemtime($path2)
                    );
                }
            }
        }
    }
    return $files;
}

function getUrl($_channel, $type, $order, $page, $system = 0, $layout = 0, $color = 0, $language = 0)
{
    switch ($_channel) {
        case 'jquery':
            $path = '/' . $_channel . '/' . $type . '-' . $order . '-' . $page . '.html';
            break;
        case 'source':
            $path = '/' . $_channel . '/' . $type . '-' . $system . '-' . $layout . '-' . $order . '-' . $page . '.html';
            break;
        case 'templates':
            $path = '/' . $_channel . '/' . $type . '-' . $layout . '-' . $color . '-' . $language . '-' . $order . '-' . $page . '.html';
            break;
    }

    return $path;
}


function getChannelName($channel)
{
    $channels = [
        'jquery' => '网页特效', 'source' => '整站源码', 'templates' => '网页模板',
    ];

    return $channels[$channel];
}

function getSourceById($id)
{
    $data = SourceAttrs::getById($id);
    return $data;
}

function getSourceAttr($channel)
{

    $_layout = [
        'source'    => SourceAttrs::getByName('source', 'layout'),
        'templates' => SourceAttrs::getByName('templates', 'layout'), 'jquery' => [], 'plugins' => [],
    ];

    $_color = [
        'templates' => SourceAttrs::getByName('templates', 'color'), 'jquery' => [], 'source' => [], 'plugins' => []
    ];

    $_type = [
        'jquery'    => SourceAttrs::getByName('jquery', 'type'), 'source' => SourceAttrs::getByName('source', 'type'),
        'templates' => SourceAttrs::getByName('templates', 'type'), 'plugins' => [],
    ];

    $_system = [
        'source' => SourceAttrs::getByName('source', 'system'), 'plugins' => [], 'jquery' => [], 'templates' => [],
    ];


    $attrs = [
        'type'   => $_type[$channel], 'layout' => $_layout[$channel], 'color' => $_color[$channel],
        'system' => $_system[$channel],
    ];

    return $attrs;
}

/**
 * 递归移动源目录（包括文件和子文件）到目的目录【或移动源文件到新文件】
 * @param [string] $source 源目录或源文件
 * @param [string] $target 目的目录或目的文件
 * @return boolean true
 */

function moveFolder($source, $target)
{
    if (!file_exists($source))
        return false; //如果源目录/文件不存在返回false

    //如果要移动文件
    if (filetype($source) == 'file') {
        $basedir = dirname($target);
        if (!is_dir($basedir))
            mkdir($basedir); //目标目录不存在时给它创建目录
        copy($source, $target);
        unlink($source);

    } else { //如果要移动目录

        if (!file_exists($target))
            mkdir($target); //目标目录不存在时就创建

        $files = array(); //存放文件
        $dirs  = array(); //存放目录
        $fh    = opendir($source);

        if ($fh != false) {
            while ($row = readdir($fh)) {
                $src_file = $source . '/' . $row; //每个源文件
                if ($row != '.' && $row != '..') {
                    if (!is_dir($src_file)) {
                        $files[] = $row;
                    } else {
                        $dirs[] = $row;
                    }
                }
            }
            closedir($fh);
        }

        foreach ($files as $v) {
            copy($source . '/' . $v, $target . '/' . $v);
            unlink($source . '/' . $v);
        }

        if (count($dirs)) {
            foreach ($dirs as $v) {
                moveFolder($source . '/' . $v, $target . '/' . $v);
            }
        }
    }
    return true;
}

/**
 * 递归删除目录文件
 * @param $path
 */
function delFolder($path)
{
    // 打开目录
    $dh = opendir($path);
    // 循环读取目录
    while (($file = readdir($dh)) !== false) {
        // 过滤掉当前目录'.'和上一级目录'..'
        if ($file == '.' || $file == '..')
            continue;
        // 如果该文件是一个目录，则进入递归
        if (is_dir($path . '/' . $file)) {
            delFolder($path . '/' . $file);
        } else {
            // 如果不是一个目录，则将其删除
            unlink($path . '/' . $file);
        }
    }
    // 退出循环后(此时已经删除所有了文件)，关闭目录并删除
    closedir($dh);
    rmdir($path);
}

function get_contents($file)
{
    $filename = $file;
    $handle   = fopen($filename, "r");//读取二进制文件时，需要将第二个参数设置成'rb'

    //通过filesize获得文件大小，将整个文件一下子读到一个字符串中
    $contents = fread($handle, filesize($filename));
    fclose($handle);

    return $contents;
}

/**
 * AES 解密
 * @param $data
 * @return mixed
 */
function decrypt($data)
{
    return (new \core\AES())->decrypt($data);
}

/**
 * AES 加密
 * @param $data
 * @return string
 */
function encrypt($data)
{
    return (new \core\AES())->encrypt($data);
}


function get_today_over_time()
{
    return strtotime(date('Y-m-d 23:59:59')) - time();
}


function curl_post($url, $data)
{
    //初使化init方法
    $ch = curl_init();
    //指定URL
    curl_setopt($ch, CURLOPT_URL, $url);
    //设定请求后返回结果
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //声明使用POST方式来进行发送
    curl_setopt($ch, CURLOPT_POST, 1);
    //发送什么数据呢
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //忽略证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //忽略header头信息
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //设置超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    //发送请求
    $output = curl_exec($ch);
    //关闭curl
    curl_close($ch);
    //返回数据
    return $output;
}

/**
 * 发送邮件
 * @param $toemail
 * @param $title
 * @param $content
 * @param string $toname
 * @return array
 * @throws \PHPMailer\PHPMailer\Exception
 *
 */

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function send_email($toemail, $title, $content, $toname = '')
{

    $mail = new \PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // 启用详细调试输出
        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet    = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host       = config('app.email_smtp');// 发送方的SMTP服务器地址
        $mail->SMTPAuth   = true;// 是否使用身份验证
        $mail->Username   = config('app.email_send');/// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱
        $mail->Password   = config('app.email_pass');// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！
        $mail->SMTPSecure = $mail::ENCRYPTION_SMTPS;// 使用ssl协议方式
        $mail->Port       = config('app.email_port');// 163邮箱的ssl协议方式端口号是465/994

        $mail->setFrom(config('app.email_send'), config('app.email_name'));// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
        $mail->addAddress($toemail, $toname);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        // $mail->addReplyTo("xxx@163.com","Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        // $mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
        // $mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
        // $mail->addAttachment("bug0.jpg");// 添加附件

        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $title; // 邮件标题
        $mail->Body    = $content;  // 邮件正文
        //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
        $mail->send();
        return ['code' => 1];  //成功
        // echo "Message could not be sent.";
        // echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息

    } catch (\Exception $e) {
        return ['code' => 0, 'message' => $mail->ErrorInfo];
    }
}

function is_login()
{
    if (session('?user')) {
        return true;
    }
    return false;
}


/**
 * 获取客户端真实IP
 * @return bool
 */
function get_ip()
{
    $ip = FALSE;
    //客户端IP 或 NONE
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }
    //多重代理服务器下的客户端真实IP地址（可能伪造）,如果没有使用代理，此字段为空
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = FALSE;
        }
        for ($i = 0; $i < count($ips); $i++) {
            if (!preg_match("^(10│172.16│192.168).", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    //客户端IP 或 (最后一个)代理服务器 IP
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

/**
 * Object转array
 * @param $array
 * @return array
 */
function object_array($array)
{
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}


/**
 * 生成随机不重复的字符串
 * @return string
 */
function rand_str()
{
    return md5(uniqid(microtime(true), true));
}

/**
 * 获取指定长度由数字和字母组成的字符串
 * @param $len
 * @return string
 */
function getAssignStr($len, $loser = false, $upper = true)
{
    $chars_array = array(
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
    );
    $chars_lower = [
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z",
    ];
    $chars_upper = [
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V",
        "W", "X", "Y", "Z",
    ];

    if ($loser) {
        $chars_array = array_merge($chars_array, $chars_lower);
    }

    if ($upper) {
        $chars_array = array_merge($chars_array, $chars_upper);
    }

    $charsLen = count($chars_array) - 1;

    $outputstr = "";
    for ($i = 0; $i < $len; $i++) {
        $outputstr .= $chars_array[mt_rand(0, $charsLen)];
    }
    return $outputstr;
}

/**
 * 通用化API接口数据输出
 * @param int $status 业务状态码
 * @param string $message 信息提示
 * @param [] $data  数据
 * @param int $httpCode http状态码
 * @return array
 */
function show($status, $msg = '', $data = [], $httpCode = 200)
{
    $data = [
        'status' => $status, 'msg' => $msg, 'data' => $data,
    ];

    return json($data, $httpCode);
}

if (!function_exists('parse_array')) {
    /**
     * 将一维数组解析成键值相同的数组
     * @param array $arr 一维数组
     * @return array
     */
    function parse_array($arr)
    {
        $result = [];
        foreach ($arr as $item) {
            $result[$item] = $item;
        }
        return $result;
    }
}

if (!function_exists('parse_attr')) {
    /**
     * 解析配置
     * @param string $value 配置值
     * @return array|string
     */
    function parse_attr($value = '')
    {
        $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
        if (strpos($value, ':')) {
            $value = array();
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }
}


if (!function_exists('this_action')) {
    /**
     * 将一维数组解析成键值相同的数组
     * @param array $arr 一维数组
     * @return array
     */
    function this_action($action)
    {
        $actions = [
            'create' => '添加', 'edit' => '编辑', 'show' => '查看',
        ];
        return $actions[$action];
    }
}

/**
 * 递归删除文件
 * @param $dir
 * @return bool
 */
function del_dir($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    $handle = opendir($dir);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            is_dir("$dir/$file") ? del_dir("$dir/$file") : @unlink("$dir/$file");
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
        @rmdir($dir);
    }
}

if (!function_exists('get_file_path')) {
    /**
     * 获取附件路径
     * @param int $id 附件id* @returnstring
     */
    function get_file_path($id = 0)
    {
        $model = new \app\model\Attachments;
        $path  = $model->getFilePath($id);
        if (!$path) {
            return '/admin/img/none.png';
        }
        return $path;
    }
}


if (!function_exists('get_thumb')) {
    /**
     * 获取图片缩略图路径
     * @param int $id 附件id
     * @returnstring
     */
    function get_thumb($id = 0, $type = 0)
    {
        $model = new \app\model\Attachments;
        $path  = $model->getThumbPath($id);
        if (!$path) {
            return $type ? '/admin/img/none.png' : '/admin/img/load.png';
        }
        return $path;
    }
}


/**
 * $html-需要爬取的页面内容
 * $tag-要查找的标签
 */
function get_tag_data($html, $tag)
{
    $regex = "/<$tag.*?>(.*?)<\/$tag>/is";
    preg_match_all($regex, $html, $matches, PREG_PATTERN_ORDER);
    return $matches[1];
}


/**
 * 获取用户头像路径
 * @param int $uid 用户id
 * @return string
 */
function get_avatar($avatar, $default)
{
    if ($default && $avatar) {
        $path = '/static/images/picture/' . $avatar . '.jpg';
    } elseif ($avatar && !$default) {
        $path = \app\model\Attachments::getFilePath($avatar);
    } else {
        $path = '/admin/img/avatar.jpg';
    }
    return $path;
}

/**
 * 获取用户头像路径
 * @param int $uid 用户id
 * @return string
 */
function get_avatar_by_id($id)
{
    $user    = \app\model\Users::getInfo($id, 'avatar,default_avatar');
    $avatar  = $user['avatar'];
    $default = $user['default_avatar'];
    return get_avatar($avatar, $default);
}

/**
 * 根据附件id获取文件名
 * @param string $id 附件id* @returnstring
 */
function get_file_name($id = '')
{
    $name = model('\app\model\Attachments')->getFileName($id);
    if (!$name) {
        return '没有找到文件';
    }
    return $name;
}


function get_cat_name($id)
{
    $name = \think\Db::table('categorys')->where('id', $id)->value('name');
    return $name;
}