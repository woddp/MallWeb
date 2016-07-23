<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/20
 * Time: 18:37
 */

namespace Home\Controller;


use Think\Controller;

class ArticleController extends FatherController
{


    //帮助文章
    public  function helps(){
        $id=I('get.id');

        //实例化文章分类
        $article_model=M('Article');
        $rows=$article_model->where("id=$id")->find();
      
        $this->assign('rows',$rows);
        $this->display();
    }
}