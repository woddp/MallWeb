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

//发送邮件

function send_email($useremail,$title,$content){
    //引入phpmailer类
   Vendor('PHPMailer.PHPMailerAutoload');
    //实例化邮箱
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.126.com';  //邮箱服务器地址
    $mail->SMTPAuth = true;                               // 开启smtp验证
    $mail->Username = 's717563864@126.com';                 // 发件人账户名
    $mail->Password = 'liujiang123';                           // 密码
    $mail->SMTPSecure = 'ssl';                            // 连接加密方式 若使用SSL安全链接
    $mail->Port = 465;                                    // 邮箱端口

    $mail->setFrom('s717563864@126.com', 'woddp');          //发件人账号
    $mail->addAddress($useremail);               // 收件人账号

    $mail->isHTML(true);                                  // 以html发送邮件

    $mail->Subject = $title;              //标题
    $mail->Body    = $content; //正文
    $mail->CharSet = 'UTF-8';
    if(!$mail->send()) {
       return array('status'=>'0','msg'=>$mail->ErrorInfo);
    } else {
        return array('status'=>'1','msg'=>"发送成功");
    }
}




//取出分类所有数据
function category_list($arr,$id){


    $rows=array();
    foreach ($arr as $row){
        if($row['parent_id']==$id){
            $rows[]=$row;
        }
    }

    return $rows;

}

//树形展示
function list_tree($lists,$pk='id',$pid='parent_id',$child='child',$root=0){

    //创建树形空数组
    $tree=array();
    //判断是否是个数字
    if(is_array($lists)){
        //主键数组
        $refer=array();
        //以数组id作为key
        foreach ($lists as $key=>$data){
            $refer[$data[$pk]]=&$lists[$key];
        }

        foreach ($lists as $key=>$data){
            //判断当前parent_id 是否等于根id 等级数组
            if($root==$data[$pid]){
                //是的话就把当前的数组装入树形数组
                $tree[]=&$lists[$key];  //取出了顶级数组

            }else{ //如果不是对顶级的数据
                if(isset($refer[$data[$pid]])){
                    $child_row = &$refer[$data[$pid]];//不是顶级数组
                    $child_row[$child][]=&$lists[$key];
                }
            }
        }
    }
return $tree;
}

/**
 * @param $content 文章内容
 * 获取文章内的第一歌图片地址
 */
function get_img_url($data){

foreach($data as &$row){
    $content=html_entity_decode( $row['article_content']['content']);
    $arr=array();
    preg_match('/src="(.*?\.(jpg|png|gif|jpeg))/',$content,$arr);
     if(!empty($data)){
         $row['img_url']=$arr[1];
     }
}
    //src="(.*?.(jpg|png|gif|jpeg))

   return $data;
}