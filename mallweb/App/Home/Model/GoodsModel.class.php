<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/7
 * Time: 17:43
 */

namespace Home\Model;


use Think\Model;
use Think\Page;

class GoodsModel extends Model

{

    //获取热销精品新品
    public function goods_status($i,$field='*')
    {
       return  $rows=$this->field($field)->where(array('status=1','is_on_sale=1',"goods_status&$i"))->order('id desc')->limit(0,6)->select();
    }

    //获取某个分类的热销精品新品
    public function category_goods_status($i,$field='*',$id,$lenth)
    {
        //获取分类上的边界
        $goodscategory_model=D('GoodsCategory');
        $ids=$goodscategory_model->margin($id);
        $ids=array_column($ids, 'id'); //取得id
        
        $data=[
            'goods_category_id'=>['in',$ids],
            'status'=>1,
            'is_on_sale'=>1,
            "goods_status&$i",
        ];
        $rows=$this->field($field)->where($data)->limit(0,$lenth)->order('id desc')->select();
       return $rows;

    }


    //获取分类下的商品
    public  function get_goods($goods_category_ids){

        $condition=[
          'goods_category_id'=>['in',$goods_category_ids],
            'status'=>1,
            'is_on_sale'=>1,
        ];
        $count=$this->where($condition)->count();
        $page=new Page($count,C('LISTROWS'));
        $page->setConfig('theme', C('PAGE_CONFIG')['theme']);
        $page->setConfig('first','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $rows=$this->where($condition)->page(I('get.p'),C('LISTROWS'))->order('id desc')->select();
        $show=$page->show();
        return compact('show','rows');
    }
    //获取商品和相册
    public function findRow($id)
    {

        //获取商品信息
        $goods_info=$this->find($id);
        //获取商品简介
         $intro_model=M('GoodsIntro');
         $goods_intro=$intro_model->field('content')->find($id);
        //获取商品相册
        $gallery_model=M('GoodsGallery');
        $goods_gallery=$gallery_model->field('path')->where("Goods_id={$id}")->select();
       return  array_merge($goods_info, $goods_intro,array('gallery'=>$goods_gallery));

    }




}