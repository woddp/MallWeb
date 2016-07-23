<?php
/**
 * ========================================
 * 订单管理
 * ========================================
 */

namespace Admin\Controller;


class OrderInfoController extends PublicController
{
    protected  $status=array(
        0=>'取消',
        1=>'待付款',
        2=>'待发货',
        3=>'待收货',
        4=>'完成',
    );
    //订单显示
    function index(){
       //取出所订单
        $order_model=D('OrderInfo');
        $rows=$order_model->order('inputtime desc')->select();
        $this->assign('rows',$rows);
        $this->assign('status',$this->status);
        $this->display();
    }
    //前台发货后后台就发货
    function notify($id){
        $order_info_model=D('OrderInfo');
        //把状态改为2
        $cond=[
            'status'=>2,
            'id'=>$id,
        ];
        if($order_info_model->save($cond)===false){
            $this->error('付款失败',U('OrderInfo/order'));
        }
        $this->success('付款成功',U('OrderInfo/order'));
    }

    //清除超时订单
    function clear_overtime_order(){

         $order_info_model=D('OrderInfo');
        //查询所有未付款的超时的订单
        $cond=[
            'status'=>1,
            'inputtime'=>['lt',time()-3600],//订单时间 当前时间减去3600
        ];
        $rows=$order_info_model->where($cond)->getField('id',true);


        //如果不存在就直接退出
        if(empty($rows)){
            return true;
        }
        dump($rows);
        //把过期的状态修改为0
        $order_info_model->where(['id'=>['in',$rows]])->setField('status',0);

        //查询出这些订单的商品数量
        $order_info_item_model=D('OrderInfoItem');
        $cond=[
          'order_info_id'=>['in',$rows]
        ];
        $goods_rows=$order_info_item_model->where($cond)->Field('goods_id,amount')->select();

        $data=[];
        foreach ($goods_rows as $row){
          //如果相同会被覆盖 当前数组如果已经存在
            if(!empty($data[$row['goods_id']])){
                //商品出现多次 数量相加
                $data[$row['goods_id']]+=$row['amount'];
            }else{//如果商品出现一次
                $data[$row['goods_id']]=$row['amount'];
            }
        }

        //把订单的商品数量重新返回商品库存

        $goods_model=D('Goods');
        foreach ($data as $key=>$val){
            $goods_model->where("id={$key}")->setInc('stock',$val);
        }



        return true;

    }

}