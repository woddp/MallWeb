<?php
/**
 * ==========================================
 * 文章分类 模型
 * ==========================================
 */

namespace Admin\Model;

use Think\Page;
use Think\Model;

class ArticleCategoryModel extends Model
{

    // 自动验证定义
    protected $_validate        =   array(
        //分类名不能为空
         array('name','require','名称不能为空'),
        //分类名不能重复
        array('name','','文章分类已存在','','unique','insert'),
        //简介不能小于10
        array('intro','5,50','文章简介不能小于5个字符','','length'),
        //排序必须为数字
        array('sort','number','排序必须为数字'),
    );

    /*
    * ========================================
    * 分页
    * =========================================
    */

    public function show_page(){
        $search=I('get.search');
        //查询status不是-1的数据
        $data=array('status'=>['not in','-1']);
        //

        if(!empty($search)){
            $data['name']=['like',"%{$search}%"];
        }
        //共有多少条数据
        $listAll=$this->where ($data)->count('id');
        //实例化分页
        $page=new Page($listAll,C('LISTROWS'));
        //设置分页样式
        $page->setConfig ('theme',C('PAGE_CONFIG')['theme']);
        $html_page=$page->show ();

        //查询数据
        $article_categorys = $this->where ($data)->page (I('get.p'),C('LISTROWS'))->select ();
        //如果没查到数据
        return  compact ('article_categorys','html_page');
    }

}