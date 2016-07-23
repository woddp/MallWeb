<?php
/**
 * ==================================
 *菜单控制器
 * ==================================
 */
namespace Admin\Controller;


use Think\Controller;

class MenuController extends PublicController
{
    /**
     * @var null Admin\Model\MenuModel;
     */
    private $model = null;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = D('Menu');
    }

    public function index()
    {

        //获取所有菜单
        $menu=$this->model->select();
         $this->assign('menu',$menu);
      
        $this->display();
    }

    public function store()
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model -> addMenu() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('添加成功', U('index'));
        } else {
            //获取父级菜单 (所有菜单)
            $menu = $this->model->select();
            //增加一个顶级
            array_unshift($menu, array('id' => 0, 'name' => '顶级菜单', 'parent_id' => '0'));
            $this->assign('menu', json_encode($menu));
            //获取所有权限
            $permission_model = M('permission');
            $permission = $permission_model->field('id,name,parent_id,path')->select();
            $this->assign('permission', json_encode($permission));
            //获取所有url地址
            $path = $this->model->getPath(getUrl('Admin'));
            $this->assign('path', $path);
            $this->display();
        }
    }

    public function edit()
    {

        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model -> updateMenu() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('修改成功', U('index'));

        } else {
            $id=I('get.id');
            //获取当前菜单数据
            $current_menu=$this->model->find($id);
            $this->assign('current_menu', $current_menu);
            //获取当前菜单绑定的所有权限
            $menu_permission=M('MenuPermission');
            $menu_permission_data=$menu_permission->where("menu_id={$id}")->select();
            $this->assign('menu_permission_data', json_encode($menu_permission_data));
            //获取父级菜单 (所有菜单)
            $menu = $this->model->select();
            array_unshift($menu, array('id' => 0, 'name' => '顶级菜单', 'parent_id' => '0'));
            $this->assign('menu', json_encode($menu));
            //获取所有权限
            $permission_model = M('permission');
            $permission = $permission_model->field('id,name,parent_id,path')->select();
            $this->assign('permission', json_encode($permission));
            //获取所有url地址
            $path = $this->model->getPath(getUrl('Admin'));
            $this->assign('path', $path);
            $this->display();
        }
    }

    public function destroy($id)
    {
        if($this->model->deleteMenu($id)===false){
            $this->error(get_error($this->model));
        }
        $this->success('删除成功');
    }


}