<?php
namespace Admin\Behavior;

use Think\Behavior;

class PermissionBehavior extends Behavior
{
    public function run(&$params)
    {

        //判断用户是否存在cookie
        $user_cookie = cookie('user_info');


        if (!empty($user_cookie)) {//存在
            //存在则和数据库验证token
            $admin = D('Admin');
            //如果返回false则表明当前用户cookie失效或不存在
              $status = $admin->auto_login($user_cookie);
            if (!$status) {
                //错误转到登录页面
                redirect(U('Login/index'), 3, '请登录1! 页面跳转中....');
            }


        }


        //如果验证成功继续往下执行判断用户地址权限

        //拼接当前地址
        $path = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        //添加未登录公共地址地址
        $public_path = array(
            'Admin/Login/index',
            'Admin/Login/verifyCode',
            'Admin/Login/check',
            'Admin/Login/logout',

            'Admin/Repwd/index',
            'Admin/Repwd/test',
            'Admin/Repwd/repassword',
            'Admin/Repwd/verify',
        );

        //超级管理员所有权限无需验证
        //取得当前登陆用户信息
        $user_info = user_session();
        //如果当前用户
        if (!empty($user_info) && $user_info['username'] == 'admin') {
            //并为登陆用户添加登录公用页面
            return true;
        }

        //登陆公用地址 未登录为空
        $protected_path = array();
        //如果登陆
        if (!empty($user_info)) {
            $protected_path = array(
                'Admin/Index/index',
                'Admin/Upload/upload',

            );
        }

        //用户登陆可访问地址
        $user_path = user_path();
        if (empty($user_path)) {
            $user_path = array();
        }

        //获取当前地址 并且合并公共地址
        $current_path = array_merge($public_path, $user_path, $protected_path);
        //判断当前用户是否有权限(判断url是否相等)
        if (!in_array($path, $current_path)) {
            //错误转到登录页面
            redirect(U('Login/index'), 3, '请登录! 页面跳转中....');
        }


    }


}