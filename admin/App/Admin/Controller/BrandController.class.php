<?php
/**
 * ==================================
 *品牌 控制器
 * ==================================
 */

namespace Admin\Controller;

use Think\Controller;

class  BrandController extends PublicController
{
    /**
     * @var \Admin\Model\brandModel;
     */
    private $brand_model = null;

    protected function _initialize ()
    {
        parent::_initialize();
        $this->brand_model = D ('Brand');
    }

    public function index ()
    {
       
        //获取数据
        $brands = $this->brand_model->show_page ();
       
        //显示数据
        $this->assign ('brands', $brands)->display ();
    }

    public function store ()
    {
        if (IS_POST) {
            $date=I('post.');
            if(empty(I('post.sort'))){
               unset($date['sort']);
              }
            //插入的时候才验证
            if ($this->brand_model->create ($date, 'insert') === false) {
                $this->error (get_error ($this->brand_model));
            }
            if ($this->brand_model->add () === false) {
                $this->error (get_error ($this->brand_model));
            }

            $this->success ('添加成功', U ('index'));
        } else {
            //获取商品分类
            $goodscategory_model = D('GoodsCategory');
            $goodscategory = $goodscategory_model->field('id,name,parent_id')->select();
            //转换成json
            $goodscategory = json_encode($goodscategory);
            $this->assign('goodscategory', $goodscategory);

            $this->display ();
        }
    }

    public function edit ()
    {

        if (IS_POST) {

            if ($this->brand_model->create () === false) {
                $this->error (get_error ($this->brand_model));
            }
            if ($this->brand_model->save () === false) {
                $this->error (get_error ($this->brand_model));
            }
            $this->success ('修改成功', U ('index'));
        } else {
            //获取商品分类
            $goodscategory_model = D('GoodsCategory');
            $goodscategory = $goodscategory_model->field('id,name,parent_id')->select();
            //转换成json
            $goodscategory = json_encode($goodscategory);
           
            $this->assign('goodscategory', $goodscategory);
            //获取当前品牌属于哪个商品分类
            
            $brand = $this->brand_model->find (I ('get.id'));
            $this->assign ('brand', $brand)->display ();
        }
    }

    public function destroy ($id)
    {
        if (IS_GET) {
            //根据id找到字段 把statu更改为-1 并把字段name添加字符串后缀_del
            $data = array('status' => '-1', 'name' => ['exp', 'concat(name,"_del")']);
            if ($this->brand_model->where ("id={$id}")->setField ($data) === false) {
                $this->error ('删除失败');
            }
            $this->success ('删除成功');

        } else {
            $this->display ();
        }
    }
}