<?php
/**
 * ==================================
 *菜重置密码
 * ==================================
 */
namespace Admin\Controller;


use Think\Controller;

class RepwdController extends PublicController
{

    function index()
    {
        $this->display();
    }

    function test(){
        if(IS_POST){
            //邮箱验证
            $username=I('post.username');
            $email=I('post.email');
            //判邮箱是否存在
            $admin_model=M('Admin');
            $cond=array(
                'username'=>$username,
                'email'=>$email,
            );
           $rows=$admin_model->where($cond)->select();

            //为空不存在
            if(empty($rows[0])){
                $this->error('用户名或邮箱不存在!');
            }
            session('id',$rows[0]['id']);
           //存在就重新添加密码
           $this->redirect('repassword');

        }
        $this->error('抱歉!');
    }

    function repassword(){
        //验证session是否存在

        if(session('id')){
              $this->display();
          }else{
              $this->error('抱歉!');
          }

    }

    function  verify(){
        $admin_model=D('Admin');
        if($admin_model->create()===false){
         $this->error(get_error($admin_model));
        }
        if($admin_model->resetting()===false){
            $this->error(get_error($admin_model));
        }else{
            $this->success('密码修改成功',U('index'));
        }
    }
}