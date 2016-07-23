<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/19
 * Time: 16:51
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model
{

    //分类边界
    function margin($id,$field='id,name'){
        //获取当前分类的id
        $row=$this->field('lft,rght')->find($id);
        //找出当前界限下的所有分类
        $data=[
          'lft'=>['egt',$row['lft']],
          'rght'=>['elt',$row['rght']],
        ];
        $rows=$this->where($data)->field($field)->order('lft desc')->select();
        return $rows;
    }

    //根据标识取出商品分类的id
    function  sign_id($sign){
        //获取当前分类的id
        $row=$this->field('id')->where("sign='{$sign}'")->find();
               return $row['id'];
       }



}