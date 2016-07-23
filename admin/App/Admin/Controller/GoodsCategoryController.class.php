<?php
/**
 * ==================================
 *商品分类 控制器
 * ==================================
 */

namespace Admin\Controller;

use Think\Controller;

class  GoodsCategoryController extends PublicController
{
    /**
     * @var \Admin\Model\brandModel;
     */
    private $GoodsCategory_model = null;

    protected function _initialize ()
    {
        parent::_initialize();
        $this->GoodsCategory_model = D ('GoodsCategory');
    }

    public function index ()
    {
        //获取数据
        $GoodsCategorys = $this->GoodsCategory_model->show_page ();

        //显示数据
        $this->assign ('GoodsCategorys', $GoodsCategorys)->display ();
    }

    public function store ()
    {
        if (IS_POST) {
            $date=I('post.');
            if(empty(I('post.sort'))){
               unset($date['sort']);
              }
            //插入的时候才验证
            if ($this->GoodsCategory_model->create ($date, 'insert') === false) {
                $this->error (get_error ($this->GoodsCategory_model));
            }
            if ($this->GoodsCategory_model->goodsInsert () === false) {
                $this->error (get_error ($this->GoodsCategory_model));
            }

            $this->success ('添加成功', U ('index'));
        } else {
            //商品列表
            $GoodsCategorys = $this->GoodsCategory_model->goodsRow ();
           $goodscategorys=json_encode ($GoodsCategorys);

            $this->assign ('goodscategorys',$goodscategorys)->display ();
        }
    }

    public function edit ()
    {
        if (IS_POST) {

            if ($this->GoodsCategory_model->create () === false) {
                $this->error (get_error ($this->GoodsCategory_model));
            }
            if ($this->GoodsCategory_model->goodsUpdate () === false) {
                $this->error (get_error ($this->GoodsCategory_model));
            }
            $this->success ('修改成功', U ('index'));
        } else {
            //获取需要修改的数据
            $goods = $this->GoodsCategory_model->find (I ('get.id'));

            //商品列表
            $GoodsCategorys = $this->GoodsCategory_model->goodsRow ();
            $goodscategorys=json_encode ($GoodsCategorys);
           
            $this->assign('goods',$goods);
            $this->assign ('goodscategorys', $goodscategorys);
            $this->display();
        }
    }

    public function destroy ($id)
    {
        if (IS_GET) {
            if ($this->GoodsCategory_model->DeleteGoods($id) === false) {
                $this->error (get_error ($this->GoodsCategory_model));
            }
            $this->success ('删除成功');

        } else {
            $this->display ();
        }
    }
}