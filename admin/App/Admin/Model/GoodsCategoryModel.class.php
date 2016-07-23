<?php
/**
 * ==================================
 *供货商管理 模型
 * ==================================
 */
namespace Admin\Model;


use Think\Model;
use Think\Page;

class GoodsCategoryModel extends Model
{
    // 是否批处理验证
    protected $patchValidate    =   true;

     public  $_validate=array(

           array('name','require','品牌不能为空'),

           array('name','','品牌已存在',self::VALUE_VALIDATE,'unique','insert'),

         //产品标识唯一
         array('sign','','标识已存在',self::VALUE_VALIDATE,'unique'),
         //介绍不能少于5个字
           array('intro','5,1000','品牌介绍不能少于5个字','','length'),
     );

    public  $_auto=array(
        //序列号默认22
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
        $Category = $this->where ($data)->order('lft')->select ();
        //如果没查到数据
        return  compact ('Category','html_page');
    }


    /**
     * 获取所有商品 只需要id 和商品名 并添加一条顶级分类
     */

    function  goodsRow(){
         $arr=$this->order('lft asc')->select();
         $top=array(
               'id'=>'0',
              'name' =>'顶级分类',
              'parent_id' =>0,
         );
        array_unshift ( $arr,$top);
        return $arr;
    }


    /**
     * 商品添加
     */
    function  goodsInsert(){
        $data=$this->data();
        //添加不需要id 去掉id
        unset($data['id']);
        //实例化Logic逻辑层 找到需要操作的数据库对象
        $dbsql=new \Admin\Logic\DbSqlLogic();
         //实例化nestedsets 来完成数据的操作
        //逻辑层的对象，需要操作表名 和字段

        $nest=new  \Admin\Service\NestedSets($dbsql,$this->getTableName (),'lft','rght','parent_id', $this->getPk (), 'level');
        //$parent_id, array $data = array(), $position = 'bottom'
        //父id          操作的数据    默认值
        $status=$nest->insert ($data['parent_id'],$data,'bottom');
        if(!$status){
            return false;
        }

    }


    /**
     * 商品更新
     *
     */

    function  goodsUpdate(){
        $data=$this->data();

        $id=$data['id'];
        $parent_id=$data['parent_id'];
         //判断是否是只修改信息不修改层级则不实例化$dbsql
        $parent=$this->getFieldById($id,'parent_id');
             //如果当前父id不等于取出的父id
        if($parent_id!==$parent){
        //实例化Logic逻辑层 找到需要操作的数据库对象
        $dbsql=new \Admin\Logic\DbSqlLogic();
        //实例化nestedsets 来完成数据的操作
        //逻辑层的对象，需要操作表名 和字段

        $nest=new  \Admin\Service\NestedSets($dbsql,$this->getTableName (),'lft','rght','parent_id', $this->getPk (), 'level');
        //$parent_id, array $data = array(), $position = 'bottom'
        //父id          操作的数据    默认值
        //修改的是所有元素的左右边界
        $status=$nest->moveUnder ($id, $parent_id,"bottom");
            if(!$status){
                $this->error='添加失败';
                return false;
            }
        //保存元素内容
        }
        if($this->save ()===false){
            return false;
        }
    }

    /**
     * 删除商品
     */
    function DeleteGoods($id){

        //判断商品下面是否还有子商品

        $row=$this->where (array('parent_id'=>['eq',$id]))->select ();
          //不为空
        if(!empty($row)){
            $this->error='无法删除 该商品下还有子商品！';
            return false;
        }

        //实例化Logic逻辑层 找到需要操作的数据库对象
        $dbsql=new \Admin\Logic\DbSqlLogic();
        //实例化nestedsets 来完成数据的操作
        //逻辑层的对象，需要操作表名 和字段

        $nest=new  \Admin\Service\NestedSets($dbsql,$this->getTableName (),'lft','rght','parent_id', $this->getPk (), 'level');

      $status = $nest->delete ($id);
      if(!$status){
          $this->error='删除失败';
          return false;
      }
        
    }
}