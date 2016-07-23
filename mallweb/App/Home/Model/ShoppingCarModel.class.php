<?php
/**
 * ================================================
 * 购物车
 * ================================================
 */
namespace Home\Model;


use Think\Model;

class ShoppingCarModel extends Model
{

    //把cookie存入数据库
    function cookie_add($member_id)
    {

        $rows = cookie('shop_car');
        //取出所有goods_id 商品id
        $goods_ids = array_keys($rows);

        //删除订单里重复的值
        $this->where(array('goods_id' => ['in', $goods_ids]))->delete();
        $goods = array();

        foreach ($rows as $key => $val) {
            $goods[] = array(
                'member_id' => $member_id,
                'goods_id' => $key,
                'amount' => $val,
            );
        }

        //然后添加cookie里所有订单到数据库
        $this->addAll($goods);

        //然后清除cookie shop_car 的信息
        cookie('shop_car', null);
    }

    //获取购物车的戏信息

    function get_car()
    {
        //判断是否登录
        $user = session('user');
        //或取cookie的购物车信息
        if (empty($user)) {
            $shopping_car_info = cookie('shop_car');
        } else {//获取登录的购物车信息
            $shopping_car_info = $this->where("member_id={$user['id']}")->getField('goods_id,amount');

            //获取用户的积分 来计算当前商品的优惠
            $member_model = D('Member');
            $score = $member_model->where(['id' => $user['id']])->getField('score');

            //如果新用户没积分
            if (empty($score)) {
                $score = 0;
            }
            //根据用户积分获取当前用户等级折扣
            $member_level_model = M('MemberLevel');
            $cond = [
                'bottom' => ['elt', $score],
                'top' => ['egt', $score],
            ];

            $member_level = $member_level_model->where($cond)->find();
            //折扣
            $discount = $member_level['discount'] / 100;

        };
        //取出所有goods_id 和amount 数量
        $goods_ids = array_keys($shopping_car_info);
        $amount = array_values($shopping_car_info);
        //取出goods_id对应商品
        $goods_model = D('Goods');
        //如果有商品id
        if ($goods_ids) {

            $cond = [
                'id' => ['in', $goods_ids],
                'status' => 1,
                'is_on_sale' => 1,
            ];

            $rows = $goods_model->where($cond)->field('id,name,logo,shop_price')->select();


            //总价
            $total = '0';
            foreach ($rows as $key => &$row) {

                //数量
                $row['amount'] = $amount[$key];
                //获取商品的折扣率 如果商品没有折扣率 就使用会员折扣率
                $member_goods_price_model = M('MemberGoodsPrice');
                $cond = [
                    'goods_id' => $row['id'],//当前商品
                    'member_level_id' => $member_level['id'],//当前用户等级
                ];

                $shop_prices = $member_goods_price_model->where($cond)->find();

                // 当前商品折扣价格
                $shop_price = $shop_prices['price'];
                //折扣需要的等级
                $need_member_level = $shop_prices['member_level_id'];
                //当前用户等级
                $curr_member_level = $member_level['id'];

                //如果为空 就使用用户等级默认折扣  并且用户等级id不等于
                if (empty($shop_price)) {
                    //单价$row['shop_price']
                    $row['subtotal'] = number_format($amount[$key] * $row['shop_price'] * $discount, 2, '.', '');
                } else if ($need_member_level === $curr_member_level) { //如果商品折扣等级 等于用户等级
                    $row['subtotal'] = number_format($amount[$key] * $shop_price, 2, '.', '');
                } else if (empty($user)) {   //如果用户未登录
                    $row['subtotal'] = number_format($amount[$key] * $row['shop_price'], 2, '.', '');
                }

                //总价
                $total += $row['subtotal'];
            }

        } else {
            $total = 0;
        }

        //把钱转换成千位浮点数 例子 4 => 4.00
        $total = number_format($total, 2, '.', '');

        return compact('rows', 'total');


    }

}