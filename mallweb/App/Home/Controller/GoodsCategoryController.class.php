<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/19
 * Time: 22:34
 */

namespace Home\Controller;


class GoodsCategoryController extends FatherController
{

  /**
   * @param $sign            产品标识
   * @param $parent_sign     产品父标识
   * @param string $parent_id 产品分类id
   * @param string $top_id 顶级产品
   */
    function  lists($sign,$parent_sign,$parent_id='',$top_id=''){
        //取出所有种类
      $goods_category_model=D('GoodsCategory');
        //种类
        $id=$goods_category_model->sign_id($sign);

        $class=$goods_category_model->margin($id,'*');

        $this->assign('class',$class);
        //$id 父种类不显示  并且展开栏目
      if(!empty($parent_id)){//有值
        $cur_id=$parent_id;
      }else{
        $cur_id=$id;
      }
        $this->assign('cur_id',$cur_id);

      //取出所有当前种类有的品牌
        $brand_model=D('Brand');
      //如果产品父id存在
      if(!empty($parent_id)){ //有值
       ;
        $data=[
            "goods_category_id"=>$parent_id,
            "status"=>['neq',-1],
        ];
      }elseif (!empty($top_id)){ //顶级的商品分类
        //取出商品分类下的所有子分类
        $class_ids=array_column($class, 'id');
        //在通过子分类查找出每个商品下的品牌
        $data=[
            "goods_category_id"=>['in',$class_ids],
            "status"=>['neq',-1],
        ];
      }
      else{
        $data=[
            "goods_category_id"=>$id,
            "status"=>['neq',-1],
        ];
      }
        $brand_class=$brand_model->where($data)->select();

        $this->assign('brand_class',$brand_class);
        //当前种类的 商品列表 取出商品分类和子分类
        $category=$goods_category_model->where("sign='{$parent_sign}'")->find();
        $goods_category=$goods_category_model->margin($category['id'],'*');

        //种类父类不显示
        $this->assign('parent_sign',$parent_sign);
        //$category['id'] 分类的父级id 例如: 电脑 手机 等产品 在数码分类下 数码分类的id为parernt_id
        $goods_category=list_tree($goods_category,'id','parent_id','child',$category['id']);
        $this->assign('goods_category',array_reverse($goods_category));
        //获取当前分类下的所有商品
        //当前子分类的所有id
         $goods_category_id=array_column($class,'id');
        //在去商品表找到在这些分类下的产品
        $goods_model=D('Goods');
        $goods=$goods_model->get_goods($goods_category_id);

        $this->assign('show',$goods['show']);


        //热卖 推荐 新品

        $list_best=$goods_model->category_goods_status(1,'id,name,logo,shop_price',$id,6);
        $this->assign('list_best', $list_best);
        $list_hot=$goods_model->category_goods_status(2,'id,name,logo,shop_price',$id,3);
        $this->assign('list_hot', $list_hot);
        $pc_bg_new=$goods_model->category_goods_status(4,'id,name,logo,shop_price',$id,8);
        $this->assign('pc_bg_new', $pc_bg_new);



        //页面默认展示列表
        $this->assign('goods',$goods['rows']);


        $this->display();
    }



}