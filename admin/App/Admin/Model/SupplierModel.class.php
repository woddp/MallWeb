<?php
/**
 * ==================================
 *供货商管理 模型
 * ==================================
 */
namespace Admin\Model;


use Think\Model;
use Think\Page;

class SupplierModel extends Model
{
    // 是否批处理验证
    protected $patchValidate    =   true;

     public  $_validate=array(
          //供货商不能为空
           array('name','require','自动不能为空'),
          //供货商唯一
           array('name','','供货商已存在',self::VALUE_VALIDATE,'unique','insert'),
          //介绍不能少于5个字
           array('intro','5,200','介绍不能少于5个字','','length'),
     );

    public  $_auto=array(

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
        $suppliers = $this->where ($data)->page (I('get.p'),C('LISTROWS'))->select ();
        //如果没查到数据

        return  compact ('suppliers','html_page');
    }

    /**
     * 获取所有供货商数据
     */
    public  function getSupplier(){
        //查询status不是-1的数据
        $data=array('status'=>['not in','-1']);
        //查询数据
        return $this->where ($data)->field ('id,name')->select ();
    }
}