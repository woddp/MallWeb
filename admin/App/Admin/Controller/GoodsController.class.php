<?php
/**
 *============================================
 *
 * 商品控制器
 *
 *============================================
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends PublicController
{
    /**
     * @var null Admin\Model\GoodsModel;
     */
    private $good_model = null;

    protected function _initialize()
    {
        parent::_initialize();
        $this->good_model = D('Goods');
    }

    function index()
    {
        $rows = $this->good_model->getpage();
        $this->assign('rows', $rows);

        //获取商品分类
        $goodscategory_model = D('GoodsCategory');
        $goodscategory = $goodscategory_model->field('id,name,parent_id')->select();
        array_unshift($goodscategory, array('id' => 0, 'name' => '选择商品'));
        $this->assign('goodscategory', $goodscategory);
        //获取品牌
        $brand_model = D('Brand');
        $brand = $brand_model->field('id,name,parent_id')->getBrand();
        array_unshift($brand, array('id' => 0, 'name' => '选择品牌'));
        $this->assign('brand', $brand);
        //获取供货商
        $supplier_model = D('Supplier');
        $supplier = $supplier_model->field('id,name,parent_id')->getSupplier();
        array_unshift($supplier, array('id' => 0, 'name' => '选择供货商'));
        $this->assign('supplier', $supplier);
        //状态
        $state = array(['id' => 0, 'name' => '状态'], ['id' => 1, 'name' => '精品'], ['id' => 2, 'name' => '热销'], ['id' => 4, 'name' => '新品'],);
        $this->assign('state', $state);
        //是否上架
        $is_on_sale = array(['id' => 0, 'name' => '选择'], ['id' => 1, 'name' => '上架'], ['id' => 2, 'name' => '下架'],);
        $this->assign('is_on_sale', $is_on_sale);
        $this->display();
    }

    function store()
    {
        if (IS_POST) {
            if ($this->good_model->create() === false) {
                $this->error(get_error($this->good_model));
            }
            if ($this->good_model->addGoods() === false) {
                $this->error(get_error($this->good_model));
            }
            $this->success('添加成功', U('index'));
        } else {
            //获取商品分类
            $goodscategory_model = D('GoodsCategory');
            $goodscategory = $goodscategory_model->field('id,name,parent_id')->select();
            //转换成json
            $goodscategory = json_encode($goodscategory);
            $this->assign('goodscategory', $goodscategory);

            //获取品牌
            $brand_model = D('Brand');
            $brand = $brand_model->getBrand();

            $this->assign('brand', $brand);
            //获取会员等级
            $member_level_model = M('MemberLevel');
            $member_level = $member_level_model->select();
            $this->assign('member_level', $member_level);
            //获取供货商
            $supplier_model = D('Supplier');
            $supplier = $supplier_model->getSupplier();
            $this->assign('supplier', $supplier);

            $this->display();
        }
    }

    function edit($id)
    {
        if (IS_POST) {
            if ($this->good_model->create('','') === false) {
                $this->error(get_error($this->good_model));
            }
            if ($this->good_model->updateGoods() === false) {
                $this->error(get_error($this->good_model));
            }
            $this->success('修改成功', U('index'));
        } else {
            //获取商品信息
            $goods = $this->good_model->getGoods($id);

            $this->assign('goods', $goods);
            //获取商品详情
            $goods_intro = M('GoodsIntro');
            $intro = $goods_intro->find($id);
            $this->assign('intro', $intro['content']);
            //获取商品分类
            $goodscategory_model = D('GoodsCategory');
            $goodscategory = $goodscategory_model->field('id,name,parent_id')->select();
            //转换成json
            $goodscategory = json_encode($goodscategory);
            $this->assign('goodscategory', $goodscategory);

            //获取品牌
            $brand_model = D('Brand');
            $brand = $brand_model->field('id,name,parent_id')->getBrand();
            $this->assign('brand', $brand);

            //获取供货商
            $supplier_model = D('Supplier');
            $supplier = $supplier_model->field('id,name,parent_id')->getSupplier();
            $this->assign('supplier', $supplier);

            //获取相册
            $gallery_model = M('GoodsGallery');
            $gallery = $gallery_model->where("goods_id={$id}")->select();
            $this->assign('gallery', $gallery);
            //获取会员等级
            $member_level_model = M('MemberLevel');
            $member_level = $member_level_model->select();

            //获取会员商品价格
            $member_goods_price_model = M('MemberGoodsPrice');
            $member_goods_prices = $member_goods_price_model->where("goods_id={$id}")->select();
        //把会员商品价格添加到会员等级内 修改使用
            foreach ($member_level as $key=>$val) {
                foreach ($member_goods_prices as $item) {
                    if ($val['id'] == $item['member_level_id']) {
                        $member_level[$key]['price']=$item['price'];
                    }
                }
            }
//          dump($member_level);
//            exit();
            $this->assign('member_level', $member_level);
            //获取是否是轮播置顶
            $banner_model=M('Banner');
            $banner=$banner_model->where("goods_id={$id}")->find();
          
            $this->assign('banner',$banner);


            $this->display();
        }
    }




    function destroy($id)
    {
        if (IS_GET) {
            //使用逻辑删除
            $data = array(
                'id' => $id,
                'status' => '0',
            );
            $status = $this->good_model->save($data);
            if ($status === false) {
                $this->error('删除失败');
            }
            $this->success('删除成功');

        } else {
            $this->error('抱歉！');
        }
    }

    //ajax移除相册图片
    function removeImg($id)
    {
        //删除当前id的图片
        $gallery = M('GoodsGallery');
        $status = $gallery->delete($id);
        if ($status) {
            echo json_encode(array('status' => '1'));
        } else {
            echo json_encode(array('status' => '0'));
        }
    }


}