<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/11/10
 * Time: 14:35
 */

namespace app\index\controller;


use app\model\PropertyExpLogs;
use app\model\UsersLevels;
use app\model\Users;
use think\facade\Db;

class Oauth extends Common
{

    const GITHUB_CLIENT_ID     = 'Iv1.80e3a54177b233d6';
    const GITHUB_CLIENT_SECRET = '2ec31e5e2ede75fe64c2f279855003a48a1aa631';

    public function github_login()
    {
        $url = 'https://github.com/login/oauth/authorize?client_id=' . self::GITHUB_CLIENT_ID . '&redirect_uri=https://it1024.me/oauth/github&&scope=user:email';
        $this->redirect($url);
    }

    // 用户登录
    public function github()
    {
        if (input('code/s')) {
            $access_token_url = 'https://github.com/login/oauth/access_token';
            $params           = array(
                'client_id' => self::GITHUB_CLIENT_ID, 'client_secret' => self::GITHUB_CLIENT_SECRET,
                'code'      => $_GET['code'],
            );
            $access_token     = curl_post($access_token_url, $params);
            file_put_contents('./uploads/log.txt', $access_token, FILE_APPEND);
            if ($access_token) {
                $_token = array();
                parse_str($access_token, $_token);
                $token     = $_token['access_token'];
                $url       = "https://api.github.com/user?access_token=" . $token;
                $headers[] = 'Authorization: token ' . $token;
                $headers[] = "User-Agent: it1024源码-it1024素材网";
                $result    = getHttpResponseGET($url, $headers);
                $info      = json_decode($result, true);
                if (isset($info['id'])) {
                    Db::startTrans();
                    try {
                        $user_id            = Users::where('username', 'IT' . $info['id'])->value('id');
                        $time               = time();
                        $data['login_time'] = $time;
                        $data['login_ip']   = get_ip();

                        if (!$user_id) {
                            $data['username']    = 'IT' . $info['id'];
                            $data['nickname']    = $info['login'];
                            $data['create_time'] = $time;
                            $data['update_time'] = $time;
                            $code                = getAssignStr(5, true);
                            $data['code']        = $code;
                            $data['avatar']      = mt_rand(1, 120);
                            $data['password']    = md5($code . $info['login']);
                            $id                  = Users::insertGetId($data);
                            $data['avatar']      = get_avatar($data['avatar'], 1);
                            $points              = 30;
                            $exp                 = 30;
                            $property_exp_log    = [
                                'user_id'     => $id, 'title' => '注册用户', 'points' => $points, 'exp' => $exp,
                                'state'       => 1, 'create_time' => time()
                            ];
                            $user_save           = [
                                'points' => Db::raw('points+' . $points), 'exp' => Db::raw('exp+' . $exp),
                            ];
                            $level               = UsersLevels::getLevel(30);
                            PropertyExpLogs::insert($property_exp_log);
                            Users::where('id', $id)->update($user_save);
                            $data['id'] = $id;
                            $data       = array_merge($data, $level);
                        } else {
                            Users::where('id',$user_id)->update($data);
                            $data = Users::getBaseInfo($user_id);
                        }
                        unset($data['password']);
                        session('user', $data);
                        Db::commit();
                    } catch (\Exception $e) {
                        $this->error('网络繁忙~');
                        Db::rollback();
                    }

                    $this->success('登录成功');
                }
            }
        }
    }
}