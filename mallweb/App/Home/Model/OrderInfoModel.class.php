<?php
/**
 *====================================
 * 订单表
 *====================================
 */

namespace Home\Model;


use Think\Model;

class OrderInfoModel extends Model\RelationModel
{


    //添加订单
    function add_order()
    {
        $this->startTrans();

        $data = I('post.');

//============================================================================
        //获取用户收货地址信息
        $address_model = D('Address');
        $address = $address_model->field('name,province_name,city_name,area_name,detail_address,tel,member_id')->where("id={$data['address_id']}")->find();

        //获取送货方式
        $delivery_model = M('Delivery');
        $delivery = $delivery_model->field('name as delivery_name,price as delivery_price')->where("id={$data['delivery_id']}")->find();

        //price 总金额 总商品
        $shoppingcar_model = D('ShoppingCar');
        $shopping_car = $shoppingcar_model->get_car();

        //如果购物车没商品
        if (empty($shopping_car['rows'])) {
            $this->error = '没有商品哦';
            return false;
        }

//============================================================================
        $goods_model = D('Goods');
        //判断用户下单商品数量,判断库存是否足够
        foreach ($shopping_car['rows'] as $row) {
            $cond[] = [
                'id' => $row['id'],
                'stock' => ['egt', $row['amount']],//库存大于等于购买的商品数量
            ];
        }
        $cond['_logic'] = 'OR';

        $goods_row = $goods_model->where($cond)->getField('name', true);
        $error_msg = '';
        //判断是否数量足够
        foreach ($shopping_car['rows'] as $row) {
            //如果不存在 表示数量不足
            if (!in_array($row['name'], $goods_row)) {
                $error_msg .= $row['name'];
            }
        }

        //商品不足
        if (!empty($error_msg)) {
            $this->error = $error_msg . '商品不足';
            return false;
        }

        //减少商品库存
         foreach ($shopping_car['rows']  as $row){
             $goods_model->where("id={$row['id']}")->setDec('stock',$row['amount']);
         }


        //商品价格加 快递费用
        $price = ($shopping_car['total'] * 1 + $delivery['delivery_price'] * 1);
        //存储用户积分 (共消费多少钱)
        //先获取原始积分
        $member_model = D('Member');
        $score = $member_model->field('score')->find($address['member_id']);
        $score = $score['score'];
        if (empty($score)) {
            $score = $price;
        } else {
            $score += $price;
        }

        $member_data = [
            'id' => $address['member_id'],
            'score' => $score,
        ];
        //保存当前用户积分
        if ($member_model->save($member_data) === false) {
            $this->error = '购买失败';
            $this->rollback();
            return false;
        }
         //订单价格
        $this->data['price'] = number_format($price, 2, '.', '');
        //订单时间
        $this->data['inputtime']=time();
        $this->data = array_merge($this->data, $address, $delivery);

        //存储订单信息 并获取订单id
        if (($order_info_id = $this->add()) === false) {
            $this->error = '购买失败';
            $this->rollback();
            return false;
        }


//============================================================================
        //订单详情表
        $data_order = array();
        foreach ($shopping_car['rows'] as $car) {
            $data_order[] = [
                'order_info_id' => $order_info_id,
                'goods_id' => $car['id'],
                'goods_name' => $car['name'],
                'logo' => $car['logo'],
                'price' => $car['shop_price'],
                'amount' => $car['amount'],
                'total_price' => $car['subtotal'],
                'add_time' => time(),
            ];

        }
        $order_info_item_model = M('OrderInfoItem');
        //存储订单详情数据
        if ($order_info_item_model->addAll($data_order) === false) {
            $this->error = '购买失败';
            $this->rollback();
            return false;

        }
//============================================================================
        //获取发票信息


        //获取发票名字
        $data_invoice = array();
        //用户名字 如果个人
        if ($data['invoice_type'] == 1) {
            $data_invoice['name'] = $address['name'];
        } else { //公司 名称
            $data_invoice['name'] = $data['invoice_address'];
        }

        //发票内容
        $invoice_content = $data['invoice_detailed'];
        //内容
        $receipt_content = '';
        /**
         *   woddp
         *
         *   苹果  4.00 x 3  12.00
         *   橡胶  3.00 x 1  3.00
         *
         *   总计: 15.00
         */
        switch ($invoice_content) {

            case 1:
                foreach ($shopping_car['rows'] as $row) {
                    $receipt_content .= $row['name'] . "&nbsp;&nbsp;" . $row['shop_price'] . ' * ' . $row['amount'] . "&nbsp;&nbsp;" . $row['subtotal'] . "<br/>";
                }

                break;
            case 2:
                $receipt_content .= '办公用品';
                break;
            case 3:
                $receipt_content .= '体育休闲';
                break;
            default :
                $receipt_content .= '耗材';
                break;

        }
        $content = $address['name'] . "<br/>" . $receipt_content . "总计:&nbsp;&nbsp;" . $shopping_car['total'];

        $data_invoice['content'] = $content;
        $data_invoice['price'] = $shopping_car['total'];
        $data_invoice['inputtime'] = time();
        $data_invoice['member_id'] = $address['member_id'];
        $data_invoice['order_info_id'] = $order_info_id;
        //存储发票
        $invoice_model = M('Invoice');
        if ($invoice_model->add($data_invoice) === false) {
            $this->error = '购买失败';
            $this->rollback();
            return false;

        }

        //清空购物车
        //当前用户

        if ($shoppingcar_model->where("member_id={$address['member_id']}")->delete() === false) {
            $this->error = '购买失败';
            $this->rollback();
            return false;

        }

        $this->commit();
        return true;
    }
}