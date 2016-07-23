<?php
/**
 * ==================================
 * 公共控制器
 * ==================================
 */
namespace Admin\Controller;


use Think\Controller;

class PublicController extends Controller
{

    protected  function _initialize(){
        //用户信息
       $user_info=user_session() ;
       $user_name=$user_info['username'];
       $this->assign('name',$user_name);
        //如果当前用户为admin就全部菜单都显示
        if($user_name['username']!='admin'){
            $menu_model=M('Menu');
            //权限路径
            $menu=$menu_model->field('id,name,path,parent_id')->order('rght desc')->select();
            $this->assign('menus',$menu);
        }

       
    }

}