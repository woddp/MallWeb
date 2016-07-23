<?php
/**
 * ==================================
 *权限 控制器
 * ==================================
 */

namespace Admin\Controller;


use Think\Controller;

class PermissionController extends PublicController
{
    /**
     * @var null Admin\Model\PermissionModel;
     */
    private $model = null;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = D('Permission');
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
            if ($this->model->addPermission() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('添加成功', U('index'));
        } else {
            //取得所有url地址
            $urls = $this->model->getPath(getUrl('Admin'));
            $this->assign('urls', $urls);
            //取得所有权限
            $permission = $this->model->select();
            array_unshift($permission, array('id' => '0', 'name' => '顶级权限', 'pid' => '0'));
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
            if ($this->model->updatePermission() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('修改成功', U('index'));
        } else {
            //取得当前修改的权限地址
            $rows=$this->model->find($id);
            $this->assign('rows',$rows);
            //取得所有url地址
            $urls = $this->model->getPath(getUrl('Admin'));
            $this->assign('urls', $urls);
            //取得所有权限
            $permission = $this->model->select();
            array_unshift($permission, array('id' => '0', 'name' => '顶级权限', 'pid' => '0'));
            $this->assign('permission', json_encode($permission));

            $this->display();
        }
    }


    public function destroy()
    {

        if($this->model->deletePermission()===false){
            $this->error(get_error($this->model));
        }
        $this->success('删除成功', U('index'));

    }

}