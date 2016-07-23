<?php
/**
 *============================================
 *
 * 商品模型
 *
 *============================================
 */
namespace Admin\Model;

use Think\Model\RelationModel;
use Think\Page;

class GoodsModel extends RelationModel
{
    // 是否批处理验证
    protected $patchValidate = true;

    // 自动验证定义
    protected $_validate = array(
        //商品名不能为空
        array('name', 'require', '商品名不能为空'),
        //商品分类不能为空
        array('goods_category_id', 'require', '选择商品分类'),
        //货号存在则验证 不能重复
//        array('sn', '', '货号已经存在', self::VALUE_VALIDATE, 'unique'),
        //市场价格 金额不合法 不能为空
        array('market_price', 'currency', '市场价格不合的'),
        array('market_price', 'require', '市场价格不能为空'),
        //本店价格 金额不合法  不能为空
        array('shop_price', 'currency', '本店价格不合的'),
        array('shop_price', 'require', '本店价格不能为空'),
        //库存不能为空 必须为整数
        array('stock', 'require', '库存不能为空'),
        array('stock', '1,1000000', '库存不能为负数', '', 'length'),
    );
    // 自动完成定义
    protected $_auto = array(
        //添加sn
        array('goods_status', 'array_sum', 3, 'function'),
        array('sn', 'create_sn', 1, 'callback'),
        array('inputtime', 'time', 3, 'function'),

    );

    //关联表
    protected $_link = array(
        //商品分类
        'goodscategory' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'GoodsCategory',
            'foreign_key' => 'goods_category_id',
            'mapping_fields' => 'name',
        ),
        //品牌
        'brand' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Brand',
            'foreign_key' => 'brand_id',
            'mapping_fields' => 'name',
        ),
        //供货商
        'supplier' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Supplier',
            'foreign_key' => 'supplier_id',
            'mapping_fields' => 'name',
        ),
    );


    /**
     * @param $sn 获取sn 商品货号的值
     */
    function create_sn($sn)
    {

        //如果货号存在
        if ($sn) {
            return $sn;
        }
        //当天时间
        $day = date('Ymd');
        //往商品添加次数添加纪录
        $good_day_count = D('GoodsDayCount');
        //判断当天时间是否存在
        $count = $good_day_count->getFieldByDay($day, 'count');
        if ($count) {//如果今天不是第一次添加商品 则更新记录
            ++$count;
            $data = ['day' => $day, 'count' => $count];
            $good_day_count->save($data);
        } else {//第一次添加商品 则添加纪录
            $count = 1;
            $data = ['day' => $day, 'count' => $count];
            $good_day_count->add($data);
        }
        //拼接sn
        $sn = 'sn' . $day . str_pad($count, 5, '0', STR_PAD_LEFT);
        return $sn;
    }

    /**
     * 分页显示
     */
    function getpage()
    {
        $data = I('get.');
        $condition = array(
            'status' => ['gt', 0]
        );
        //不等于零按条件搜索 等于零搜索全部
        //商品分类
        if ($data['goods_category_id'] * 1 !== 0) {
            $condition[] = array(
                'goods_category_id' => $data['goods_category_id'],
            );
        }
        //品牌
        if ($data['brand_id'] * 1 !== 0) {
            $condition[] = array(
                'brand_id' => $data['brand_id'],
            );
        }
        //供应商
        if ($data['supplier_id'] * 1 !== 0) {
            $condition[] = array(
                'supplier_id' => $data['supplier_id'],
            );
        }
        //商品状态
        if ($data['goods_status'] * 1 !== 0) {
//            $condition[]=array(
//                'goods_status'=>$data["goods_status"]
//            );
            $condition[] = 'goods_status & ' . $data['goods_status'];
        }
        //是否上架
        if ($data['is_on_sale'] * 1 !== 0) {
            $is_on_sale = $data['is_on_sale'] == 2 ? '0' : 1;
            $condition[] = array(
                'is_on_sale' => $is_on_sale,
            );
        }
        if ($data['search'] != "") {
            $condition['name'] = ['like', "%{$data['search']}%"];

        }


        $totalRows = $this->relation(true)->where($condition)->count();

        $page = new Page($totalRows, C(LISTROWS));
        $page->setConfig('theme', C('PAGE_CONFIG')['theme']);
        $html = $page->show();
        //获取状态为1的分页数据
        $goods = $this->relation(true)->where($condition)->page(I('get.p'), C(LISTROWS))->select();
        //获取商品状态
        foreach ($goods as $key => $value) {

            $value['new'] = $value['goods_status'] & 4 ? 1 : false;
            $value['hot'] = $value['goods_status'] & 2 ? 1 : false;
            $value['best'] = $value['goods_status'] & 1 ? 1 : false;
            $goods[$key] = $value;
        }
        return compact('goods', 'html');
    }

    /**
     * 添加商品
     * @return bool
     */

    public function addGoods()
    {
        //开启事务
        $this->startTrans();
        //获取商品简介
        $content = I('post.content', '', false);



        //保存商品信息
        $goods_id = $this->add();
        if (!$goods_id) {
            $this->error = '商品保存失败';
            $this->rollback();
            return false;
        }
        //保存商品简介
        $intro_data = [
            'goods_id' => $goods_id,
            'content' => $content,
        ];
        $goods_intro = M('GoodsIntro');
        $goods_intro_id = $goods_intro->add($intro_data);
        if (!$goods_intro_id) {
            $this->error = '商品简介失败';
            $this->rollback();
            return false;
        }
        //保存相册
        $gallery = I('post.gallery');
        $data = array();
        foreach ($gallery as $row) {
            $data[] = array(
                'goods_id' => $goods_id,
                'path' => $row,
            );
        }
        //不为空才执行添加相册
        if (!empty($gallery)) {
            $gallery_model = M('GoodsGallery');
            $gallery_id = $gallery_model->addAll($data);
            if (!$gallery_id) {
                $this->error = '相册失败';
                $this->rollback();
                return false;
            }
        }

        //保存会员商品打折
        $member_prices = I('post.discount');
        $member_goods_price_model = M('MemberGoodsPrice');
        //会员商品价格数据
        $member_goods_price = array();
        foreach ($member_prices as $key => $val) {
            //如果某个会员等级未设定价格就跳过
            if (empty($val)) {
                continue;
            }
            $member_goods_price[] = [
                'goods_id' => $goods_id,
                'member_level_id' => $key,
                'price' => $val,
            ];

        }
        //保存会员价 数据不为空
        if ($member_goods_price&&($member_goods_price_model->addAll($member_goods_price) === false)) {
            $this->error = '会员价格保存失败';
            $this->rollback();
            return false;
        }


        //判断是否是banner
        $banner_model=M('Banner');
        $img_url=I('post.img_url');
        if(I('post.banner')==1&&!empty($img_url)){
            //保存当前商品首页置顶轮播
                $data=[
                    'goods_id'=>$goods_id,
                    'img_url'=>$img_url,
                ];
                $banner_model->add($data);
        }

        $this->commit();
        return true;
    }

    /**
     * 修改商品
     */

    function updateGoods()
    {
        $this->startTrans();
        $data = I('post.', '', false);

        //保存商品
        $id = $this->save($this->data());
        if ($id === false) {
            $this->error = '商品添加失败';
            $this->rollback();
            return false;
        }


        //保存商品详情
        $intro = array(
            'goods_id' => $data['id'],
            'content' => img_url($data['content']),
        );

        $goods_intro = M('GoodsIntro');
        $intro_id = $goods_intro->save($intro);

        if ($intro_id === false) {
            $this->error = '商品详情失败';
            $this->rollback();
            return false;
        }
        //保存相册
        $gallery = I('post.gallery');
        $data = array();
        foreach ($gallery as $row) {
            $data[] = array(
                'goods_id' => I('post.id'),
                'path' => $row,
            );
        }
        $gallery_model = M('GoodsGallery');

        $gallery_id = $gallery_model->addAll($data);
        if (!$gallery_id && !empty($data)) {
            $this->error = '相册失败';
            $this->rollback();
            return false;
        }

        //修改会员商品打折
        $member_prices = I('post.discount');
        $member_goods_price_model = M('MemberGoodsPrice');
        //先删除旧数据
        $id=I('post.id');
        $member_goods_price_model->where(['goods_id'=>$id])->delete();
        //会员商品价格数据
        $member_goods_price = array();
        foreach ($member_prices as $key => $val) {
            //如果某个会员等级未设定价格就跳过
            if (empty($val)) {
                continue;
            }
            $member_goods_price[] = [
                'goods_id' => $id,
                'member_level_id' => $key,
                'price' => $val,
            ];

        }
        //修改会员价  会员数据不为空
        if ($member_goods_price && ($member_goods_price_model->addAll($member_goods_price) === false)) {
            $this->error = '会员价格保存失败';
            $this->rollback();
            return false;
        }

        //判断是否是banner
        $banner_model=M('Banner');
       if(I('post.banner')==1){
               $data=[
                   'id'=>I('post.banner_id'),
                  'goods_id'=>$id,
                  'img_url'=>I('post.img_url'),
               ];
               $banner_model->save($data);
       }else{ //取消轮播
           //查询当前id是否已经是轮播
           $row=$banner_model->where("goods_id={$id}")->find();
           //如果存在则删除
           if($row){
                $banner_model->where("goods_id={$id}")->delete();
           }
       }


        $this->commit();

        return true;
    }

    /**
     * 获取一条商品信息
     */

    function getGoods($id)
    {
        $data = $this->find($id);
        $tmp = [];
        //获取商品状态
        if ($data['goods_status'] & 4) {
            $tmp[] = 4;
        }
        if ($data['goods_status'] & 2) {
            $tmp[] = 2;
        }
        if ($data['goods_status'] & 1) {
            $tmp[] = 1;
        }
        //去掉空数组
        $data['goods_status'] = json_encode($tmp);

        return $data;
    }


}