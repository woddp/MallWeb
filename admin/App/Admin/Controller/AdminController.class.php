<?php
/**
 * ========================================
 * 管理员
 * ========================================
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends PublicController
{
    /**
     * @var null Admin\Model\AdminModel;
     */
    private $model = null;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = D('Admin');
    }

    public function index()
    {
        //获取数据
        $admin = $this->model->show_page ();

        //显示数据
        $this->assign ('admin', $admin)->display ();

    }

    public function store()
    {
        if (IS_POST) {
            if($this->model->create()===false){
                $this->error(get_error($this->model));
            }
            if($this->model->addAdmin()===false){
                $this->error(get_error($this->model));
            }
            $this->success('添加成功',U('index'));


        } else {
            //取出所有角色
            $role_model = M('Role');
            $role = $role_model->select();
            $this->assign('role', json_encode($role));
            $this->display();
        }
    }

    public function edit($id)
    {
        if (IS_POST) {
            if($this->model->create()===false){
                $this->error(get_error($this->model));
            }
            if($this->model->updateAdmin()===false){
                $this->error(get_error($this->model));
            }
            $this->success('修改成功',U('index'));
        } else {
            //取出所有管理员
            $admin=$this->model->find($id);
            $this->assign('admin',$admin);
            //取出当前管理员的角色权限表
   
            $admin_role=$this->model->get_admin_role($id);
            $this->assign('admin_role', json_encode($admin_role));
            //取出所有角色
            $role_model = M('Role');
            $role = $role_model->select();
            $this->assign('role', json_encode($role));
            $this->display();
        }
    }

    public function destroy($id)
    {
        //删除
        if($this->model->deleteAdmin($id)===false){
            $this->error('删除失败');
        }
        $this->success('删除成功');

    }

}