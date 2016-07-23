<?php
/**
 *========================================
 *购物车
 *========================================
 */

namespace Home\Controller;

use Think\Controller;

class ShoppingCarController extends FatherController
{

    //参照主流的购物车设计方案(京东),完成购物车的数据存放方式的设计

    function shopping_car()
    {

        $goods_id=I('post.goods_id');
        $amount=I('post.amount');
        //如果没有登录就存放在cookie中
        if (empty(session('user'))) {

            $key = 'shop_car';
            $car_list = cookie($key);
            /**
             * [
             *  'goods_id'=>amount,
             *  'goods_id'=>amount,
             * ]
             * 假设我们的需求是:
             * 如果cookie中已经有了此商品,再在详情页添加,其实是增加商品的数量
             */
            if (isset($car_list[$goods_id])) {
                $car_list[$goods_id] += $amount;
            } else {
                $car_list[$goods_id] = $amount;
            }
            cookie('shop_car', $car_list, 604800);//保存一周

            $this->success('商品添加成功', U('ShoppingCar/flow1'));
            exit();

        } else {
            //当前用户id
            $member_id = session('user')['id'];
            //实例化订单库
            $shop_model = D('ShoppingCar');

            //获取cookie的订单存入数据库
            $str = cookie('shop_car');
            //如果cookie有订单
            if ($str) {
                //把cookie里的值添加数据库
                $shop_model->cookie_add($member_id);
            }
            //如果数据库存在就更新
            //存在就获取商品订单id
            $shops = $shop_model->where(array('goods_id' => $goods_id))->find();

            //接受登录用户的数据
            if ($shops) { //存在就更新
                $data = array(
                    'id' => $shops['id'],
                    'goods_id' => $goods_id,
                    'amount' => $amount + $shops['amount'],
                    'member_id' => $member_id,
                );
                $shop_model->save($data);
            } else {//没有就添加
                $data = array(
                    'goods_id' => $goods_id,
                    'amount' => $amount,
                    'member_id' => $member_id,
                );
                $shop_model->add($data);
            }

            $this->success('商品添加成功', U('ShoppingCar/flow1'));

        }

    }

    //购物车
    function flow1()
    {


        $shop_model = D('ShoppingCar');
        //获取购物车所有的商品
        $data = $shop_model->get_car();
        $this->assign('goods', $data);
        //我的购物车 核对订单信息 成功提交订单
        $my = 'flow1';
        if(IS_AJAX){
           $this->ajaxReturn($data);
        }else{
            $this->assign('my', $my);
            $this->display();
        }


    }


    //订单信息
    function flow2()
    {
        $user = session('user');

        //判断是否登录
        if (empty($user)) {
            $this->error('请登录!');
        }
        //接受订单信息
        if(IS_POST){

            $order_model=D('OrderInfo');
           //保存订单信息
            if($order_model->create()===false){
                $this->error(get_error($order_model),U('flow1'));
            }

            if($order_model->add_order()===false){
                $this->error(get_error($order_model),U('flow1'));
            }
          $this->success('购买成功','flow3');

            exit();
        }

        $id = $user['id'];
        //获取收货地址
        $address_model = D('Address');
        $address = $address_model->where("member_id=$id")->select();
        $this->assign('address', $address);
        //获取送货方式
        $delivery_model=M('Delivery');
        $delivery=$delivery_model->order('sort desc')->select();
        $this->assign('delivery',$delivery);
        //获取支付方式
        $payment_model=M('Payment');
        $payment=$payment_model->order('sort desc')->select();
        $this->assign('payment',$payment);

        $shop_model = D('ShoppingCar');
        //获取购物车所有的商品
        $data = $shop_model->get_car();
        $this->assign('goods', $data);
     
        //我的购物车 核对订单信息 成功提交订单
        $my = 'flow2';
        $this->assign('my', $my);
        $this->display();
    }

    //提交订到
    function flow3()
    {
        //我的购物车 核对订单信息 成功提交订单
        $my = 'flow3';
        $this->assign('my', $my);
        $this->display();
    }
}