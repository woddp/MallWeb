<?php

//把验证错误信息循环出
function get_error (Think\Model $model)
{
     $errors=$model->getError ();
    //如果不是数组
    if (!is_array ($errors)) {
        $errors=array($errors);
    }
   $html='<ul>';
    foreach ($errors as $error){
        $html.='<li>'.$error.'</li>';
    }
    $html.='</ul>';
    return $html;
}

/**
 * 加盐加密
 */
function get_md5($password,$salt){
   return md5(md5($password).md5($salt));
}


/**
 * @param array $data    需要下拉列表的数组数据
 * @param $value_field   value的值
 * @param $name_field    name的值
 * @param $name          表单提交的名字
 * @param $select        回显得值
 */
function html_select(array $data,$value_field,$name_field,$name,$select=null){
    $html='<select name='.$name.' class="form-control">';
    foreach ($data as $row){
        if($row[$value_field]==$select*1){
            $html.='<option selected="selected" value='.$row[$value_field].'>'.$row[$name_field].'</option>';
        }else{
            $html.='<option value='.$row[$value_field].'>'.$row[$name_field].'</option>';
        }

    }
    $html.='</select>';

    return $html;
}


//========================================
   //session cookie 封装
//===========================================
//用户信息
function user_session($data=""){
    if(empty($data)){

        return session('USERINFO');
    }else{
        session('USERINFO',$data);
    }

}
//用户访问地址
function user_path($data=""){
    if(empty($data)){
        return session('USERPATH');
    }else{
        session('USERPATH',$data);
    }

}
//用户访问地址
function user_menu($data=""){
    if(empty($data)){
        return session('USERPATH');
    }else{
        session('USERPATH',$data);
    }

}


/**
--------------------------------------------------
//获取所有模块控制器方法
--------------------------------------------------
 */

//获取所有控制器名称
function getController($module){
    if(empty($module)) return null;
    $module_path = APP_PATH . '/' . $module . '/Controller/';  //控制器路径
    if(!is_dir($module_path)) return null;
    $module_path .= '/*.class.php';
    $ary_files = glob($module_path);
    foreach ($ary_files as $file) {
        if (is_dir($file)) {
            continue;
        }else {
            $files[] = basename($file, C('DEFAULT_C_LAYER').'.class.php');
        }
    }
    return $files;
}
//获取所有方法名称
function getAction($controller){
    if(empty($controller)) return null;
    $con = A($controller);
    $functions = get_class_methods($con);
    //排除部分方法
    $inherents_functions = array('_initialize','__construct','theme','getActionName','isAjax','display','show','fetch','buildHtml','assign','__set','get','__get','__isset','__call','error','success','ajaxReturn','redirect','__destruct', '_empty');
    foreach ($functions as $func){
        if(!in_array($func, $inherents_functions)){
            $customer_functions[] = $func;
        }
    }
    return $customer_functions;
}

//获取所有模块下的所有控制器方法
function getUrl($plant){
    $modules = array($plant);  //模块名称
    $i = 0;
    foreach ($modules as $module) {
        $all_controller = getController($module);
        foreach ($all_controller as $controller) {
            $controller_name = $module.'/'.$controller;
            $all_action = getAction($controller_name);
            foreach ($all_action as $action) {
                $data[$i]['module'] = $module;
                $data[$i]['controller'] = $controller;
                $data[$i]['action'] = $action;
                $i++;
            }
        }
    }
    return $data;
}


//改图片地址加上当前网址
function img_url($data){
    //当前网址
//
//    $result = preg_replace_callback('/src=.*?\/(.*?.jpg)/',
//        function ($matches){
//            $cur_url='http://'.$_SERVER['SERVER_NAME'];
//      return 'src="'.$cur_url.'/'.$matches[1];
//        }
//        , $data);
    return $data;

}


