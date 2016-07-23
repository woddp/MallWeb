<?php
/**
 *========================================
 *商品
 *========================================
 */

namespace Home\Controller;


use Think\Cache\Driver\Redis;
use Think\Controller;

class GoodsController extends FatherController
{



    /**
     * 商品页面
     */
    public function goods()
    {
        if (IS_GET) {
            $id=(I('get.')['id'])*1;

            //传递的值不是数组
            $goods_model = D('Goods');
            //获取相册和内容
            $data = $goods_model->findRow($id);

            if(empty($data)){
                $this->error('没有这个商品',U('index'));
            }
            $this->assign('data', $data);

            //获取品牌
            $brand_model = M('brand');
            $barnd=$brand_model->field('name')->find($data['brand_id']);
            $this->assign('brand',$barnd);
            //获取当前同等级的分类

                    //1.根据商品id 找出属于的商品分类
            $goods_category_id=$data['goods_category_id'];
                    //2.在找到商品分类的parent_id 在找到相同层级的分类
                 $goods_category_model=D('GoodsCategory');
            $parent_id=$goods_category_model->where("id={$goods_category_id}")->getField('parent_id');
            $goods_category=$goods_category_model->where("parent_id={$parent_id}")->select();
            $this->assign('goods_category',$goods_category);
                     //3.获取父级标识 parent_sign
                    $top_id=$goods_category_model->where("id={$parent_id}")->getField('parent_id');
                               //1.获取当前产品的顶级产品标识
            $parent_sign=$goods_category_model->where("id={$top_id}")->getField('sign');
            $this->assign('parent_sign',$parent_sign);

            //当前商品类的热销
            $hot=$goods_model->category_goods_status(2,'id,name,logo,shop_price',$parent_id,4);
            $this->assign('hot', $hot);
            $best=$goods_model->category_goods_status(1,'id,name,logo,shop_price',$parent_id,4);
            $this->assign('best', $best);
          

            $this->display();
        }
    }

    //商品点击次数
    public function goods_click($id)
    {

        //如果配置click_num为true使用redis存储点击
        if (C('CLICK_NUM')) {
            $redis = new Redis();
            //把click_times这位redis主键自动增加 不存在创建
            $num = $redis->zIncrBy('click_times', 1, $id);
        } else {

            $model = M('GoodsClick');
            //读取数据 获取点击数
            $row = $model->find($id);
            //不存在就添加从0开始
            if (empty($row)) {
                $num = 1;
                $data = array(
                    'goods_id' => $id,
                    'click_times' => $num,
                );
                $model->add($data);
            } else { //存在就取出原有数据 在加1
                $num = $row['click_times'];
                ++$num;
                $data = array(
                    'goods_id' => $row['goods_id'],
                    'click_times' => $num,
                );
                $model->save($data);
                $num = $data['click_times'];
            }
        }

        $this->ajaxReturn($num);
    }

    //把redis的点击数量存入数据库

    function add_click()
    {
        //实例化redis
        $redis = new Redis();
        //获取所有redis里的商品点击量数据
        $data = $redis->zRange('click_times', 0, -1, true);
        //如果数据为空则返回
        if(empty($data)){
            return true;
        }
        //把goods_id取出来
        $ids = array_keys($data);
        //先把要添加的数据在数据库删除
        $goods_model = M('GoodsClick');
        $goods_model->where(array('goods_id' => ['in', $ids]))->delete();

        $data = array();
        foreach ($data as $key => $value) {
            $data[] = array(
                'goods_id' =>$key,
                'click_times'=>$value,
         );
        }
        //添加到数据库
        $goods_model->addAll($data);

    }


    function GoodsCategory($id){
        $goodscategory_model=D('GoodsCategory');
        $goodscategory_model->margin($id);


    }


}