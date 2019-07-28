<?php
/**
 * @classname Login
 * @author   Bob<bob@bobcoder.cc>
 * @version   1$
 * @link https://www.bobcoder.cc/
 * @Date  2019/7/23
 */

namespace app\api\controller;


use app\api\model\Admin;
use app\api\model\User;
use think\Cache;
use think\Controller;

class Login extends Controller
{

    /**
     * 管理员登录
     *
     * @author Bob<bob@bobcoder.cc>
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/23
     * @throws \think\exception\DbException
     */
    public function admin_login()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');

        print_r($this->request->post());die;

        $hasUser = Admin::where('username', $username)->find();
        if (!$hasUser) {
            return json(['code' => 0, 'data' => '', 'msg' => '管理员不存在']);
        }
        if (md5(md5($password) . config('auth_key')) != $hasUser['password']) {
            writelog($hasUser['id'], $username, '用户【' . $username . '】登录失败：密码错误', 2);
            return json(['code' => 0, 'data' => '', 'msg' => '密码错误']);
        }
        if (1 != $hasUser['status']) {
            writelog($hasUser['id'], $username, '用户【' . $username . '】登录失败：该账号被禁用', 2);
            return json(['code' => 0, 'data' => '', 'msg' => '该账号被禁用']);
        }
        // 直接创建token并设置有效期
        $hasUser['auth'] = 'admin';
        $token = md5(json_encode($hasUser).time());
        Cache::set($token, json_encode($hasUser), 7 * 86400);

        $access = [
            'access_token' => $token,
            'token_type' => "Bearer",
            'expires_in' => strtotime("+30 day"),
        ];

        return json(['code' => 1, 'data' => $access, 'msg' => '登录成功']);
    }

    /**
     * 用户登录
     *
     * @author Bob<bob@bobcoder.cc>
     * @link https://www.bobcoder.cc/
     * @throws \think\exception\DbException
     * @Date 2019/7/23
     */
    public function login()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');

        $hasUser = User::where('account', $username)->find();
        if (!$hasUser) {
            return json(['code' => 0, 'data' => '', 'msg' => '用户不存在']);
        }
        if (md5(md5($password) . config('auth_key')) != $hasUser['password']) {
            writelog($hasUser['id'], $username, '用户【' . $username . '】登录失败：密码错误', 2);
            return json(['code' => 0, 'data' => '', 'msg' => '密码错误']);
        }
        if (1 != $hasUser['status']) {
            writelog($hasUser['id'], $username, '用户【' . $username . '】登录失败：该账号被禁用', 2);
            return json(['code' => 0, 'data' => '', 'msg' => '该账号被禁用']);
        }
        // 直接创建token并设置有效期
        $hasUser['auth'] = 'member';
        $token = md5(json_encode($hasUser).time());
        Cache::set($token, json_encode($hasUser), 7 * 86400);

        $access = [
            'access_token' => $token,
            'token_type' => "Bearer",
            'expires_in' => strtotime("+30 day"),
        ];

        return json(['code' => 1, 'data' => $access, 'msg' => '登录成功']);
    }
}