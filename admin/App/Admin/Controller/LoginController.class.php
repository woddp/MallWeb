<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/4
 * Time: 11:55
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Verify;

class LoginController extends PublicController
{
    public function index()
    {
        $this->display();
    }

    public function verifyCode()
    {
        $config = array('length' => 4);
        $verify = new Verify($config);
        $verify->entry();
    }

    public  function check(){
        //判断登录输入是否合法
        $admin_model=D('Admin');
        if($admin_model->create('','login')===false){
            $this->error(get_error($admin_model));
        }
        if($admin_model->loginCheck()===false){
            $this->error(get_error($admin_model));
        }
        $this->success('登录成功',U('Index/index'));
    }

    /**
     * 退出
     */
    public  function logout(){
         //清空session
        session(null);
        cookie(null);
        $this->success('退出成功',U('Login/index'));
    }

}