<?php
/**
 * ==================================
 *角色控制器
 * ==================================
 */
namespace Admin\Controller;


use Think\Controller;

class RoleController extends PublicController
{

    /**
     * @var null Admin\Model\RoleModel;
     */
    private $model = null;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = D('Role');
    }

    public function index()
    {
        $rows = $this->model->getPage();
        $this->assign('rows', $rows);
        $this->display();
    }

    public function store()
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model->addRole() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('添加成功', U('index'));
        } else {
            //取得所有权限
            $permission = D('Permission');
            $permission = $permission->field('id,name,parent_id')->select();
            $this->assign('permission', json_encode($permission));
            $this->display();
        }
    }

    public function edit($id)
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model->updateRole() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('修改成功', U('index'));

        } else {
            //取得当前修改的数据
            $role=$this->model->relation(true)->find($id);
            $this->assign('role',$role);
            //把权限转换成json
            $this->assign('roelPerssion',json_encode($role['roelPerssion']));
            //取得所有权限
            $permission = D('Permission');
            $permission = $permission->field('id,name,parent_id')->select();
            $this->assign('permission', json_encode($permission));
            $this->display();
        }
    }

    public function destroy($id)
    {
         
            //删除role角色
            $this->model->delete($id);
            //删除角色权限
             $role_permission = M('RolePermission');
             $role_permission->where("role_id={$id}")->delete();
            $this->success('删除成功');

    }


}