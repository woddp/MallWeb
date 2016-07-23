<?php
/**
 * ==================================
 *品牌 模型
 * ==================================
 */
namespace Admin\Model;


use Think\Model;
use Think\Page;

class BrandModel extends  Model\RelationModel
{
    // 是否批处理验证
    protected $patchValidate    =   true;

     public  $_validate=array(
          //供货商不能为空
           array('name','require','品牌不能为空'),

          //介绍不能少于5个字
           array('intro','5,1000','品牌介绍不能少于5个字','','length'),
     );

    public  $_auto=array(
        //序列号默认22
    );


    //关联模型
     protected  $_link=array(
               'goods_category'=>array(
                  'mapping_type'=>self::BELONGS_TO,
                   'class_name'=>'GoodsCategory',
                   'foreign_key'=>'goods_category_id',
                   'mapping_fields'=>'name',
               ),

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
        $brand = $this->Relation(true)->where ($data)->page (I('get.p'),C('LISTROWS'))->select ();
        //如果没查到数据

        return  compact ('brand','html_page');
    }

    /**
     * 获取所有品牌数据
     */
    public  function getBrand(){
        //查询status不是-1的数据
        $data=array('status'=>['not in','-1']);
        //查询数据
       return $this->Relation(true)->where ($data)->field ('id,name,goods_category_id')->select ();
    }
}