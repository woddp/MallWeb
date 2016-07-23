<?php
/**
 * ==================================
 *供货商管理 控制器
 * ==================================
 */

namespace Admin\Controller;

use Think\Controller;

class SupplierController extends PublicController
{
    /**
     * @var \Admin\Model\SupplierModel;
     */
    private $supplier_model = null;

    protected function _initialize ()
    {
        parent::_initialize();
        $this->supplier_model = D ('Supplier');
    }

    public function index ()
    {
        //获取数据
        $suppliers = $this->supplier_model->show_page ();
        //显示数据
        $this->assign ('suppliers', $suppliers)->display ();
    }

    public function store ()
    {
        if (IS_POST) {
            //插入的时候才验证
            if ($this->supplier_model->create ('', 'insert') === false) {
                $this->error (get_error ($this->supplier_model));
            }
            if ($this->supplier_model->add () === false) {
                $this->error (get_error ($this->supplier_model));
            }
            $this->success ('修改成功', U ('index'));
        } else {
            $this->display ();
        }
    }

    public function edit ()
    {
        if (IS_POST) {

            if ($this->supplier_model->create () === false) {
                $this->error (get_error ($this->supplier_model));
            }
            if ($this->supplier_model->save () === false) {
                $this->error (get_error ($this->supplier_model));
            }
            $this->success ('修改成功', U ('index'));
        } else {
            //获取需要修改的数据
            $supplier = $this->supplier_model->find (I ('get.id'));
            $this->assign ('supplier', $supplier)->display ();
        }
    }

    public function destroy ($id)
    {
        if (IS_GET) {
            //根据id找到字段 把statu更改为-1 并把字段name添加字符串后缀_del
            $data = array('status' => '-1', 'name' => ['exp', 'concat(name,"_del")']);
            if ($this->supplier_model->where ("id={$id}")->setField ($data) === false) {
                $this->error ('删除失败');
            }
            $this->success ('删除成功');

        } 
    }
}